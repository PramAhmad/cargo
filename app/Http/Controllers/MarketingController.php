<?php

namespace App\Http\Controllers;

use App\Models\Marketing;
use App\Models\Bank;
use App\Models\MarketingGroup;
use App\Models\MarketingBank;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MarketingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the search query and filters
        $search = $request->input('search');
        $groupFilter = $request->input('group_id');
        
        // Build the query with relationships
        $marketingsQuery = Marketing::with(['banks', 'marketingGroup', 'user']);
        
        // Apply search filter if search term is provided
        if ($search) {
            $marketingsQuery->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone1', 'like', "%{$search}%");
            });
        }
        
        // Apply group filter
        if ($groupFilter) {
            $marketingsQuery->where('marketing_group_id', $groupFilter);
        }
        
        // Order and paginate results
        $marketings = $marketingsQuery->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString(); // This preserves the search parameter in pagination links
        
        // Get marketing groups for the filter dropdown
        $marketingGroups = MarketingGroup::orderBy('name')->get();
            
        return view('backend.marketings.index', compact('marketings', 'search', 'groupFilter', 'marketingGroups'));
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
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'borndate' => 'nullable|date',
            'email' => 'required|email|max:255|unique:users,email',
            'website' => 'nullable|url|max:255',
            'ktp' => 'nullable|file|mimes:jpeg,jpg,png,gif,pdf|max:2048',
            'npwp' => 'nullable|string|max:30',
            'requirement' => 'nullable|string',
            'address_tax' => 'nullable|string',
            'due_date' => 'nullable|integer|min:0',
            'marketing_group_id' => 'required|exists:marketing_groups,id',
            'status' => 'required|boolean',
            
            // Bank accounts
            'bank_accounts' => 'nullable|array',
            'bank_accounts.*.bank_id' => 'nullable|exists:banks,id',
            'bank_accounts.*.rek_name' => 'nullable|string|max:255',
            'bank_accounts.*.rek_no' => 'nullable|string|max:50',
            'bank_accounts.*.is_default' => 'nullable|boolean',
            
            // User account fields
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
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
            
            // Handle file upload
            $ktpPath = null;
            if ($request->hasFile('ktp')) {
                $file = $request->file('ktp');
                $fileName = time() . '_' . $file->getClientOriginalName();
                // Store file directly in public/ktp directory
                $uploadPath = public_path('ktp');
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                $file->move($uploadPath, $fileName);
                $ktpPath = $fileName;
            }
            
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone1,
                'status' => UserStatus::ACTIVE,
            ]);
            
            // Assign marketing role
            $user->assignRole('marketing');
            
            // Create marketing record
            $marketingData = $request->except(['password', 'password_confirmation', 'bank_accounts', 'ktp']);
            $marketingData['user_id'] = $user->id;
            $marketingData['ktp'] = $ktpPath;
            
            $marketing = Marketing::create($marketingData);
            
            // Save bank accounts
            if ($request->has('bank_accounts')) {
                foreach ($request->bank_accounts as $bankAccount) {
                    if (!empty($bankAccount['bank_id']) || !empty($bankAccount['rek_no'])) {
                        $marketing->banks()->create([
                            'bank_id' => $bankAccount['bank_id'] ?? null,
                            'rek_name' => $bankAccount['rek_name'] ?? null,
                            'rek_no' => $bankAccount['rek_no'] ?? null,
                            'is_default' => isset($bankAccount['is_default']) ? true : false,
                        ]);
                    }
                }
            }
            
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
        $marketing->load(['banks.bank', 'marketingGroup', 'user']);
        
        $recentShipments = $marketing->shipments()
            ->with(['customer', 'mitra', 'warehouse'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('backend.marketings.show', compact('marketing', 'recentShipments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Marketing $marketing)
    {
        $banks = Bank::orderBy('name')->get();
        $marketingGroups = MarketingGroup::orderBy('name')->get();
        $marketing->load(['banks.bank']);
        
        return view('backend.marketings.edit', compact('marketing', 'banks', 'marketingGroups'));
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
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'borndate' => 'nullable|date',
            'email' => 'required|email|max:255|unique:users,email,'.$marketing->user_id.',id',
            'website' => 'nullable|url|max:255',
            'ktp' => 'nullable|file|mimes:jpeg,jpg,png,gif,pdf|max:2048', 
            'npwp' => 'nullable|string|max:30',
            'requirement' => 'nullable|string',
            'address_tax' => 'nullable|string',
            'due_date' => 'nullable|integer|min:0',
            'marketing_group_id' => 'required|exists:marketing_groups,id',
            'status' => 'required|boolean',
            
            // Bank accounts
            'bank_accounts' => 'nullable|array',
            'bank_accounts.*.id' => 'nullable|exists:marketing_banks,id',
            'bank_accounts.*.bank_id' => 'nullable|exists:banks,id',
            'bank_accounts.*.rek_name' => 'nullable|string|max:255',
            'bank_accounts.*.rek_no' => 'nullable|string|max:50',
            'bank_accounts.*.is_default' => 'nullable|boolean',
            
            // User account
            'password' => 'nullable|min:8',
            'password_confirmation' => 'nullable|same:password',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try {
            // Start transaction
            DB::beginTransaction();
            
            // Handle file upload
            if ($request->hasFile('ktp')) {
                // Delete old file if exists
                if ($marketing->ktp) {
                    $oldFilePath = public_path('ktp/' . $marketing->ktp);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                
                // Upload new file
                $file = $request->file('ktp');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $uploadPath = public_path('ktp');
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                $file->move($uploadPath, $fileName);
                $marketing->ktp = $fileName;
            }
            
            // Update marketing (except ktp which we handled separately)
            $marketing->update($request->except(['password', 'password_confirmation', 'bank_accounts', 'ktp']));
            
            // User account handling
            $user = User::findOrFail($marketing->user_id);
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone1,
            ];
            
            // Only update password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            
            $user->update($userData);
            
            // Bank accounts handling
            if ($request->has('bank_accounts')) {
                // Get existing bank account IDs
                $existingBankIds = $marketing->banks->pluck('id')->toArray();
                $updatedBankIds = [];
                
                foreach ($request->bank_accounts as $bankData) {
                    // Check if this is an existing bank account or new one
                    if (isset($bankData['id']) && in_array($bankData['id'], $existingBankIds)) {
                        $bank = MarketingBank::find($bankData['id']);
                        
                        if (!empty($bankData['bank_id']) || !empty($bankData['rek_no'])) {
                            $bank->update([
                                'bank_id' => $bankData['bank_id'] ?? null,
                                'rek_name' => $bankData['rek_name'] ?? null,
                                'rek_no' => $bankData['rek_no'] ?? null,
                                'is_default' => isset($bankData['is_default']) ? true : false,
                            ]);
                            $updatedBankIds[] = $bankData['id'];
                        }
                    } else {
                        // This is a new bank account
                        if (!empty($bankData['bank_id']) || !empty($bankData['rek_no'])) {
                            $newBank = $marketing->banks()->create([
                                'bank_id' => $bankData['bank_id'] ?? null,
                                'rek_name' => $bankData['rek_name'] ?? null,
                                'rek_no' => $bankData['rek_no'] ?? null,
                                'is_default' => isset($bankData['is_default']) ? true : false,
                            ]);
                            $updatedBankIds[] = $newBank->id;
                        }
                    }
                }
                
                // Delete any bank accounts that weren't in the request
                foreach ($existingBankIds as $existingId) {
                    if (!in_array($existingId, $updatedBankIds)) {
                        MarketingBank::destroy($existingId);
                    }
                }
                
                // Ensure only one default bank
                $defaultBanks = $marketing->banks()->where('is_default', true)->get();
                if ($defaultBanks->count() > 1) {
                    // Keep only the first default bank
                    $firstDefault = $defaultBanks->first();
                    foreach ($defaultBanks as $bank) {
                        if ($bank->id !== $firstDefault->id) {
                            $bank->update(['is_default' => false]);
                        }
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('marketings.index')
                ->with('success', 'Marketing has been updated successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Marketing $marketing)
    {
        try {
            DB::beginTransaction();
            
            // Delete KTP file if exists
            if ($marketing->ktp) {
                $filePath = public_path('ktp/' . $marketing->ktp);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            
            // Delete related bank accounts
            $marketing->banks()->delete();
            
            // Delete marketing
            $marketing->delete();
            
            DB::commit();
            
            return redirect()->route('marketings.index')
                ->with('success', 'Marketing has been deleted successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('marketings.index')
                ->with('error', 'An error occurred: ' . $e->getMessage());
    }
}
    }