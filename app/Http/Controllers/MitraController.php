<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Bank;
use App\Models\MitraGroup;
use App\Models\User;
use App\Enums\UserStatus;
use App\Models\CountryMitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MitraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mitras = Mitra::with(['bank', 'mitraGroup', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('backend.mitras.index', compact('mitras'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $banks = Bank::orderBy('name')->get();
        $mitraGroups = MitraGroup::orderBy('name')->get();
        
        // Get countries list for select2
        $countries = $this->getCountriesList();
        
        return view('backend.mitras.create', compact('banks', 'mitraGroups', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Set validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'mitra_group_id' => 'required|exists:mitra_groups,id',
            'address_office_indo' => 'nullable|string',
            'country' => 'nullable|array',
            'country.*' => 'string|max:100',
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'ktp' => 'nullable|string|max:20',
            'npwp' => 'nullable|string|max:30',
            'tax_address' => 'nullable|string',
            'birthdate' => 'nullable|date',
            'borndate' => 'nullable|date',
            'created_date' => 'nullable|date',
            'bank_id' => 'nullable|exists:banks,id',
            'no_rek' => 'nullable|string|max:50',
            'atas_nama' => 'nullable|string|max:255',
            'syarat_bayar' => 'nullable|integer|min:0',
            'batas_tempo' => 'nullable|integer|min:0',
            'status' => 'required|boolean',
        ];
        
        // Add user account validation rules if checkbox is checked
        if ($request->has('create_account')) {
            $rules = array_merge($rules, [
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|min:8',
                'password_confirmation' => 'required|same:password',
            ]);
        }
        
        // Validate the request
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Generate a unique code
        $code = Mitra::generateMitraCode();
        
        try {
            // Start transaction
            DB::beginTransaction();
            
            // Create the mitra (exclude countries and password fields)
            $mitraData = $request->except(['country', 'password', 'password_confirmation', 'create_account']);
            $mitra = Mitra::create(array_merge($mitraData, ['code' => $code]));
            
            // Store countries in country_mitra table if provided
            if ($request->has('country') && is_array($request->country)) {
                foreach ($request->country as $countryName) {
                    CountryMitra::create([
                        'mitra_id' => $mitra->id,
                        'name' => $countryName,
                    ]);
                }
            }
            
            // Create user account if checkbox is checked
            if ($request->has('create_account')) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone1,
                    'status' => UserStatus::ACTIVE,
                ]);
                
                // Assign role
                $user->assignRole('mitra');
                
                // Link user to mitra
                $mitra->update(['user_id' => $user->id]);
            }
            
            DB::commit();
            
            return redirect()->route('mitras.index')->with('success', 'Mitra created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Mitra $mitra)
    {
        $mitra->load(['bank', 'mitraGroup', 'user']);
        
        return view('backend.mitras.show', compact('mitra'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mitra $mitra)
    {
        $banks = Bank::orderBy('name')->get();
        $mitraGroups = MitraGroup::orderBy('name')->get();
        $countries = $this->getCountriesList();
        
        return view('backend.mitras.edit', compact('mitra', 'banks', 'mitraGroups', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mitra $mitra)
    {
        // Set validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'mitra_group_id' => 'required|exists:mitra_groups,id',
            'address_office_indo' => 'nullable|string',
            'country' => 'nullable|array',
            'country.*' => 'string|max:100',
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'ktp' => 'nullable|string|max:20',
            'npwp' => 'nullable|string|max:30',
            'tax_address' => 'nullable|string',
            'birthdate' => 'nullable|date',
            'borndate' => 'nullable|date',
            'created_date' => 'nullable|date',
            'bank_id' => 'nullable|exists:banks,id',
            'no_rek' => 'nullable|string|max:50',
            'atas_nama' => 'nullable|string|max:255',
            'syarat_bayar' => 'nullable|integer|min:0',
            'batas_tempo' => 'nullable|integer|min:0',
            'status' => 'required|boolean',
        ];
        
        // Add user account validation rules if checkbox is checked
        if ($request->has('create_account')) {
            $uniqueRule = $mitra->user_id ? 'unique:users,email,'.$mitra->user_id.',id' : 'unique:users,email';
            $passwordRule = $mitra->user_id ? 'nullable|min:8' : 'required|min:8';
            
            $rules = array_merge($rules, [
                'email' => 'required|email|max:255|'.$uniqueRule,
                'password' => $passwordRule,
                'password_confirmation' => 'same:password',
            ]);
        }
        
        // Validate the request
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try {
            // Start transaction
            DB::beginTransaction();
            
            // Update mitra with data except sensitive fields
            $mitra->update($request->except(['country', 'password', 'password_confirmation', 'create_account']));
            
            // Update countries
            // First, delete existing records
            CountryMitra::where('mitra_id', $mitra->id)->delete();
            
            // Then add the new ones
            if ($request->has('country') && is_array($request->country)) {
                foreach ($request->country as $countryName) {
                    CountryMitra::create([
                        'mitra_id' => $mitra->id,
                        'name' => $countryName,
                    ]);
                }
            }
            
            // Handle user account
            if ($request->has('create_account')) {
                if ($mitra->user_id) {
                    // Update existing user
                    $user = User::find($mitra->user_id);
                    
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
                    
                } else {
                    // Create new user
                    $user = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => Hash::make($request->password),
                        'phone' => $request->phone1,
                        'status' => UserStatus::ACTIVE,
                    ]);
                    
                    // Assign role
                    $user->assignRole('mitra');
                    
                    // Link user to mitra
                    $mitra->update(['user_id' => $user->id]);
                }
            } else if ($mitra->user_id && !$request->has('create_account')) {
                // If checkbox is unchecked but mitra had a user account before
                // We need to unlink the user from the mitra
                $mitra->update(['user_id' => null]);
                
                // We don't delete the user account to avoid data loss
            }
            
            DB::commit();
            
            return redirect()->route('mitras.index')->with('success', 'Mitra updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mitra $mitra)
    {
        try {
            // Soft delete the mitra
            $mitra->delete();
            
            return redirect()->route('mitras.index')->with('success', 'Mitra deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('mitras.index')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
    /**
     * Get a list of all countries for select2.
     *
     * @return array
     */
    private function getCountriesList()
    {
        return [
            "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", 
            "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", 
            "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", 
            "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", 
            "Burkina Faso", "Burundi", "Cabo Verde", "Cambodia", "Cameroon", "Canada", 
            "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", 
            "Congo", "Costa Rica", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Denmark", 
            "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", 
            "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini", "Ethiopia", "Fiji", "Finland", 
            "France", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", 
            "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Honduras", "Hungary", 
            "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", 
            "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, North", 
            "Korea, South", "Kosovo", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", 
            "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", 
            "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", 
            "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", 
            "Montenegro", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", 
            "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Macedonia", 
            "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", 
            "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", 
            "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", 
            "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", 
            "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", 
            "Solomon Islands", "Somalia", "South Africa", "South Sudan", "Spain", "Sri Lanka", 
            "Sudan", "Suriname", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", 
            "Tanzania", "Thailand", "Timor-Leste", "Togo", "Tonga", "Trinidad and Tobago", 
            "Tunisia", "Turkey", "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", 
            "United Arab Emirates", "United Kingdom", "United States", "Uruguay", 
            "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam", "Yemen", 
            "Zambia", "Zimbabwe"
        ];
    }
}