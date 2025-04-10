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
    public function index()
    {
        $customers = Customer::with(['bank', 'marketing', 'customerGroup', 'customerCategory', 'user'])
            ->orderBy('name', 'asc')
            ->paginate(10);
            
        return view('backend.customers.index', compact('customers'));
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
        // Validate the request data
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
            
            // Update customer
            $customer->update($request->all());
            
            // If there's a user associated with this customer, update user's basic info too
            if ($customer->users_id && $customer->user) {
                $user = $customer->user;
                $user->name = $customer->name;
                $user->email = $customer->email;
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
