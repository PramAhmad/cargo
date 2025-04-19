<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Bank;
use App\Models\CustomerGroup;
use App\Models\CategoryCustomer;
use App\Models\Marketing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserStatus;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the search query and filters
        $search = $request->input('search');
        $typeFilter = $request->input('type');
        $marketingFilter = $request->input('marketing_id');
        
        // Build the query
        $customersQuery = Customer::with(['bank', 'marketing', 'customerGroup', 'customerCategory', 'user']);
        
        // Apply search filter if search term is provided
        if ($search) {
            $customersQuery->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone1', 'like', "%{$search}%");
            });
        }
        
        // Apply type filter
        if ($typeFilter) {
            $customersQuery->where('type', $typeFilter);
        }
        
        // Apply marketing filter
        if ($marketingFilter) {
            $customersQuery->where('marketing_id', $marketingFilter);
        }
        
        // Order and paginate results
        $customers = $customersQuery->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString(); // This preserves the search parameter in pagination links
            
        // Get data for filters    
        $marketings = Marketing::where('status', 1)->orderBy('name')->get();
            
        return view('backend.customers.index', compact('customers', 'search', 'typeFilter', 'marketingFilter', 'marketings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $banks = Bank::orderBy('name')->get();
        $customerGroups = CustomerGroup::orderBy('name')->get();
        $customerCategories = CategoryCustomer::orderBy('name')->get();
        $marketings = Marketing::where('status', 1)->orderBy('name')->get();
        
        return view('backend.customers.create', compact('banks', 'customerGroups', 'customerCategories', 'marketings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data for customer
        $validator = Validator::make($request->all(), [
            'code' => 'nullable|string|max:20|unique:customers',
            'name' => 'required|string|max:255',
            'type' => 'required|in:individual,company,internal',
            'marketing_id' => 'required|exists:marketings,id',
            'customer_group_id' => 'required|exists:customer_groups,id',
            'customer_category_id' => 'required|exists:category_customers,id',
            'bank_id' => 'nullable|exists:banks,id',
            'status' => 'required|boolean',
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:users,email',
            'website' => 'nullable|url|max:255',
            'borndate' => 'nullable|date',
            'street1' => 'nullable|string|max:255',
            'street2' => 'nullable|string|max:255',
            'street_item' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'no_rek' => 'nullable|string|max:50',
            'atas_nama' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:30',
            'tax_address' => 'nullable|string',
            'created_date' => 'nullable|date',
            
            // User account fields
            'create_user' => 'nullable|boolean',
            'password' => 'nullable|required_if:create_user,1|min:8',
            'password_confirmation' => 'nullable|required_if:create_user,1|same:password',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('customers.create')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Start transaction
            DB::beginTransaction();
            
            // Generate customer code if not provided
            if (!$request->code) {
                $request->merge(['code' => Customer::generateCustomerCode()]);
            }
            
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
                
                // Assign basic customer role
                if ($user) {
                    $user->assignRole('customer');
                    $userId = $user->id;
                }
            }
            
            // Create customer record
            $customerData = $request->except(['create_user', 'password', 'password_confirmation']);
            if ($userId) {
                $customerData['users_id'] = $userId;
            }
            
            Customer::create($customerData);
            
            DB::commit();
            
            return redirect()
                ->route('customers.index')
                ->with('success', 'Customer has been created successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('customers.create')
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $customer->load(['bank', 'marketing', 'customerGroup', 'customerCategory', 'user']);
        
        return view('backend.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $banks = Bank::orderBy('name')->get();
        $customerGroups = CustomerGroup::orderBy('name')->get();
        $customerCategories = CategoryCustomer::orderBy('name')->get();
        $marketings = Marketing::where('status', 1)->orderBy('name')->get();
        $users = User::orderBy('name')->get();
        
        return view('backend.customers.edit', compact('customer', 'banks', 'customerGroups', 'customerCategories', 'marketings', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:20|unique:customers,code,' . $customer->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:individual,company,internal',
            'marketing_id' => 'required|exists:marketings,id',
            'customer_group_id' => 'required|exists:customer_groups,id',
            'customer_category_id' => 'required|exists:category_customers,id',
            'bank_id' => 'nullable|exists:banks,id',
            'status' => 'required|boolean',
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'borndate' => 'nullable|date',
            'street1' => 'nullable|string|max:255',
            'street2' => 'nullable|string|max:255',
            'street_item' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'no_rek' => 'nullable|string|max:50',
            'atas_nama' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:30',
            'tax_address' => 'nullable|string',
            'created_date' => 'nullable|date',
            'users_id' => 'nullable|exists:users,id',
            'password' => 'nullable|min:8',
            'password_confirmation' => 'nullable|same:password',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('customers.edit', $customer->id)
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Start transaction
            DB::beginTransaction();
            
            // Handle user account
            $userId = $customer->users_id;
            
            // Case 1: Customer doesn't have a user account but we want to create one
            if (!$customer->users_id && $request->password) {
                // Check if there's already a user with this email
                $existingUser = User::where('email', $request->email)->first();
                
                if ($existingUser) {
                    $userId = $existingUser->id;
                } else {
                    // Create new user if email exists
                    if ($request->email) {
                        $user = User::create([
                            'name' => $request->name,
                            'email' => $request->email,
                            'password' => Hash::make($request->password),
                            'phone' => $request->phone1,
                            'status' => UserStatus::ACTIVE,
                        ]);
                        
                        // Assign customer role
                        $user->assignRole('customer');
                        $userId = $user->id;
                    }
                }
            } 
            // Case 2: Customer has a user account and we want to update password
            elseif ($customer->users_id && $request->password) {
                $user = User::find($customer->users_id);
                if ($user) {
                    $user->password = Hash::make($request->password);
                    $user->save();
                }
            }
            
            // Prepare data for update (exclude certain fields)
            $customerData = $request->except(['password', 'password_confirmation']);
            
            // Set user ID if we have one
            if ($userId) {
                $customerData['users_id'] = $userId;
            }
            
            // Update customer
            $customer->update($customerData);
            
            // Update associated user if exists
            if ($customer->users_id && $customer->user) {
                $user = $customer->user;
                $user->name = $customer->name;
                
                // Only update email if it changed and doesn't conflict
                if ($customer->email && $customer->email !== $user->email) {
                    $emailExists = User::where('email', $customer->email)
                        ->where('id', '!=', $user->id)
                        ->exists();
                    
                    if (!$emailExists) {
                        $user->email = $customer->email;
                    }
                }
                
                $user->phone = $customer->phone1;
                $user->save();
            }
            
            DB::commit();
            
            return redirect()
                ->route('customers.index')
                ->with('success', 'Customer has been updated successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('customers.edit', $customer->id)
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();
            
            return redirect()
                ->route('customers.index')
                ->with('success', 'Customer has been deleted successfully');
        } catch (\Exception $e) {
            return redirect()
                ->route('customers.index')
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
