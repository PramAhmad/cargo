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
        // Validate the request
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:20|unique:marketings,code,'.$marketing->id,
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'borndate' => 'nullable|date',
            'email' => 'required|email|max:255|unique:users,email,'.$marketing->user_id.',id',
            'website' => 'nullable|url|max:255',
            'ktp' => 'nullable|string|max:20',
            'npwp' => 'nullable|string|max:30',
            'requirement' => 'nullable|string',
            'address_tax' => 'nullable|string',
            'due_date' => 'nullable|integer|min:0',
            'bank_id' => 'nullable|exists:banks,id',
            'marketing_group_id' => 'required|exists:marketing_groups,id',
            'no_rek' => 'nullable|string|max:50',
            'atas_nama' => 'nullable|string|max:255',
            'status' => 'required|boolean',
            
            // Password validation - only required if not empty or if user doesn't exist
            'password' => $marketing->user_id ? 'nullable|min:8' : 'required|min:8',
            'password_confirmation' => $marketing->user_id ? 'nullable|same:password' : 'required|same:password',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Start transaction
        DB::beginTransaction();
        try {
            // Update marketing info
            $marketing->update([
                'code' => $request->code,
                'name' => $request->name,
                'address' => $request->address,
                'city' => $request->city,
                'phone1' => $request->phone1,
                'phone2' => $request->phone2,
                'borndate' => $request->borndate,
                'email' => $request->email,
                'website' => $request->website,
                'ktp' => $request->ktp,
                'npwp' => $request->npwp,
                'requirement' => $request->requirement,
                'address_tax' => $request->address_tax,
                'due_date' => $request->due_date,
                'bank_id' => $request->bank_id,
                'marketing_group_id' => $request->marketing_group_id,
                'no_rek' => $request->no_rek,
                'atas_nama' => $request->atas_nama,
                'status' => $request->status,
            ]);
            
            // Handle user account
            if ($marketing->user_id) {
                // Update existing user
                $user = User::find($marketing->user_id);
                $userData = [
                    'name' => $request->name,
                    'email' => $request->email,
                ];
                
                // Only update password if provided
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }
                
                $user->update($userData);
                
            } else if ($request->filled('password')) {
                // Create new user
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'status' => UserStatus::ACTIVE,
                ]);
                
                // Assign marketing role - you may need to adjust this based on your role management system
                $user->assignRole('marketing');
                
                // Link user to marketing
                $marketing->update(['user_id' => $user->id]);
            }
            
            DB::commit();
            return redirect()->route('marketings.index')->with('success', 'Marketing updated successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
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