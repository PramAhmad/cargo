<?php

namespace App\Http\Controllers;

use App\Models\Marketing;
use App\Models\Bank;
use App\Models\MarketingGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserStatus;
use Illuminate\Support\Facades\DB;

class MarketingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $marketings = Marketing::with(['bank', 'marketingGroup', 'user'])
            ->orderBy('name', 'asc')
            ->paginate(10);
            
        return view('backend.marketings.index', compact('marketings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $banks = Bank::orderBy('name')->get();
        $marketingGroups = MarketingGroup::orderBy('name')->get();
        
        return view('backend.marketings.create', compact('banks', 'marketingGroups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data for marketing
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:20|unique:marketings',
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'atas_nama' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'borndate' => 'nullable|date',
            'email' => 'nullable|email|max:255|unique:users,email',
            'website' => 'nullable|url|max:255',
            'ktp' => 'nullable|string|max:20',
            'npwp' => 'nullable|string|max:30',
            'requirement' => 'nullable|string',
            'address_tax' => 'nullable|string',
            'due_date' => 'nullable|integer|min:0',
            'bank_id' => 'nullable|exists:banks,id',
            'marketing_group_id' => 'required|exists:marketing_groups,id',
            'no_rek' => 'nullable|string|max:50',
            'status' => 'required|boolean',
            
            // User account fields
            'create_user' => 'nullable|boolean',
            'password' => 'nullable|required_if:create_user,1|min:8',
            'password_confirmation' => 'nullable|required_if:create_user,1|same:password',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('marketings.create')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Start transaction
            DB::beginTransaction();
            
            // Create user if requested
            $userId = null;
            if ($request->create_user && $request->email) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone1,
                    'status' => UserStatus::ACTIVE,
                ]);
                
                // Assign basic marketing role (you can modify this as needed)
                if ($user) {
                    $user->assignRole('marketing');
                    $userId = $user->id;
                }
            }
            
            // Create marketing record
            $marketingData = $request->except(['create_user', 'password', 'password_confirmation']);
            if ($userId) {
                $marketingData['user_id'] = $userId;
            }
            
            Marketing::create($marketingData);
            
            DB::commit();
            
            return redirect()
                ->route('marketings.index')
                ->with('success', 'Marketing has been created successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('marketings.create')
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Marketing $marketing)
    {
        $marketing->load(['bank', 'marketingGroup', 'user']);
        
        return view('backend.marketings.show', compact('marketing'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Marketing $marketing)
    {
        $banks = Bank::orderBy('name')->get();
        $marketingGroups = MarketingGroup::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        
        
        return view('backend.marketings.edit', compact('marketing', 'banks', 'marketingGroups', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Marketing $marketing)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:20|unique:marketings,code,' . $marketing->id,
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'atas_nama' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'borndate' => 'nullable|date',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'ktp' => 'nullable|string|max:20',
            'npwp' => 'nullable|string|max:30',
            'requirement' => 'nullable|string',
            'address_tax' => 'nullable|string',
            'due_date' => 'nullable|integer|min:0',
            'bank_id' => 'nullable|exists:banks,id',
            'marketing_group_id' => 'required|exists:marketing_groups,id',
            'user_id' => 'nullable|exists:users,id',
            'no_rek' => 'nullable|string|max:50',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('marketings.edit', $marketing->id)
                ->withErrors($validator)
                ->withInput();
        }

        // Update marketing
        $marketing->update($request->all());
        
        // If there's a user associated with this marketing, update user's basic info too
        if ($marketing->user_id && $marketing->user) {
            $user = $marketing->user;
            $user->name = $marketing->name;
            $user->email = $marketing->email;
            $user->phone = $marketing->phone1;
            $user->save();
        }
        
        return redirect()
            ->route('marketings.index')
            ->with('success', 'Marketing has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Marketing $marketing)
    {
        $marketing->delete();
        
        return redirect()
            ->route('marketings.index')
            ->with('success', 'Marketing has been deleted successfully');
    }
}