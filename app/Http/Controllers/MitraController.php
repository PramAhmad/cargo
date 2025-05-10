<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Bank;
use App\Models\MitraGroup;
use App\Models\User;
use App\Enums\UserStatus;
use App\Models\CountryMitra;
use App\Models\MitraBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Exports\MitrasExport;
use Maatwebsite\Excel\Facades\Excel;

class MitraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $groupFilter = $request->input('group_id');
        
        $mitrasQuery = Mitra::with(['bank', 'mitraGroup', 'user']);
        
        if ($search) {
            $mitrasQuery->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone1', 'like', "%{$search}%");
            });
        }
        
        if ($groupFilter) {
            $mitrasQuery->where('mitra_group_id', $groupFilter);
        }
        
        $mitras = $mitrasQuery->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString(); 
        
        $mitraGroups = MitraGroup::orderBy('name')->get();
            
        return view('backend.mitras.index', compact('mitras', 'search', 'groupFilter', 'mitraGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $banks = Bank::orderBy('name')->get();
        $mitraGroups = MitraGroup::orderBy('name')->get();
        
        $countries = $this->getCountriesList();
        
        return view('backend.mitras.create', compact('banks', 'mitraGroups', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'max_wg' => intval(str_replace('.', '', $request->max_wg)),
            'harga_ongkir_cbm' => intval(str_replace('.', '', $request->harga_ongkir_cbm)),
            'harga_ongkir_wg' => intval(str_replace('.', '', $request->harga_ongkir_wg)),
        ]);
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:mitras',
            'mitra_group_id' => 'required|exists:mitra_groups,id',
            'address_office_indo' => 'nullable|string',
            'country' => 'nullable|array',
            'country.*' => 'string|max:100',
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'max_wg' => 'nullable', 
            'harga_ongkir_cbm' => 'nullable', 
            'harga_ongkir_wg' => 'nullable', 
            'ktp' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:20048',
            'npwp' => 'nullable|string|max:30',
            'tax_address' => 'nullable|string',
            'birthdate' => 'nullable|date',
            'borndate' => 'nullable|date',
            'created_date' => 'nullable|date',
            'syarat_bayar' => 'nullable|integer|min:0',
            'batas_tempo' => 'nullable|integer|min:0',
            'status' => 'required|boolean',
            'bank_accounts' => 'nullable|array',
            'bank_accounts.*.bank_id' => 'nullable|exists:banks,id',
            'bank_accounts.*.rek_no' => 'nullable|string|max:50',
            'bank_accounts.*.rek_name' => 'nullable|string|max:255',
            'bank_accounts.*.is_default' => 'nullable|boolean',
        ];

        if ($request->has('create_account')) {
            $rules = array_merge($rules, [
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|min:8',
                'password_confirmation' => 'required|same:password',
            ]);
        }
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $code = $request->code ?? Mitra::generateMitraCode();
        
        try {
            DB::beginTransaction();
            
            $mitraData = $request->except(['country', 'password', 'password_confirmation', 'create_account', 'bank_accounts', 'ktp']);
            $mitraData['code'] = $code;
            
          
            if ($request->hasFile('ktp')) {
                $file = $request->file('ktp');
                $filename = time() . '_' . $file->getClientOriginalName();
                
                if (!File::exists(public_path('ktp'))) {
                    File::makeDirectory(public_path('ktp'), 0777, true);
                }
                
                $file->move(public_path('ktp'), $filename);
                
                $mitraData['ktp'] = $filename;
            }
            
            $mitra = Mitra::create($mitraData);
            
            if ($request->has('country') && is_array($request->country)) {
                foreach ($request->country as $countryName) {
                    CountryMitra::create([
                        'mitra_id' => $mitra->id,
                        'name' => $countryName,
                    ]);
                }
            }
            
            if ($request->has('bank_accounts') && is_array($request->bank_accounts)) {
                $hasDefault = false;
                $defaultBankIndex = null;
                
                foreach ($request->bank_accounts as $index => $bankData) {
                    if (!empty($bankData['is_default'])) {
                        $hasDefault = true;
                        $defaultBankIndex = $index;
                        break;
                    }
                }
                
                // If no default is specified but there are bank accounts, make the first one default
                if (!$hasDefault && count($request->bank_accounts) > 0) {
                    foreach ($request->bank_accounts as $index => $bankData) {
                        if (!empty($bankData['bank_id']) || !empty($bankData['rek_no']) || !empty($bankData['rek_name'])) {
                            $defaultBankIndex = $index;
                            break;
                        }
                    }
                }
                
                // Now create bank accounts
                foreach ($request->bank_accounts as $index => $bankData) {
                    if (empty($bankData['bank_id']) && empty($bankData['rek_no']) && empty($bankData['rek_name'])) {
                        continue;
                    }
                    
                    // Set is_default based on our earlier determination
                    $isDefault = ($index === $defaultBankIndex);
                    
                    MitraBank::create([
                        'mitra_id' => $mitra->id,
                        'bank_id' => $bankData['bank_id'] ?? null,
                        'rek_no' => $bankData['rek_no'] ?? null,
                        'rek_name' => $bankData['rek_name'] ?? null,
                        'is_default' => $isDefault,
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
                
                $user->assignRole('mitra');
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
        $mitra->load(['banks.bank', 'mitraGroup', 'user', 'warehouses.products']);
        
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
        $warehouses = $mitra->warehouses()->with('countries')->get();
        // return Mitra::with(['mitraCountry', 'mitraGroup', 'user'])
        //     ->where('id', $mitra->id)
        //     ->firstOrFail();
        
    return view('backend.mitras.edit', compact('mitra', 'banks', 'mitraGroups', 'countries','warehouses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->merge([
            'max_wg' => intval(str_replace('.', '', $request->max_wg)),
            'harga_ongkir_cbm' => intval(str_replace('.', '', $request->harga_ongkir_cbm)),
            'harga_ongkir_wg' => intval(str_replace('.', '', $request->harga_ongkir_wg)),
        ]);

        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:mitras,code,' . $id,
            'mitra_group_id' => 'required|exists:mitra_groups,id',
            'address_office_indo' => 'nullable|string',
            'country' => 'nullable|array',
            'country.*' => 'string|max:100',
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'ktp' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:20048', 
            'npwp' => 'nullable|string|max:30',
            'max_wg' => 'nullable', 
            'harga_ongkir_cbm' => 'nullable', 
            'harga_ongkir_wg' => 'nullable', 
            'tax_address' => 'nullable|string',
            'birthdate' => 'nullable|date',
            'created_date' => 'nullable|date',
            'syarat_bayar' => 'nullable|integer|min:0',
            'batas_tempo' => 'nullable|integer|min:0',
            'status' => 'required|boolean',
            'bank_accounts' => 'nullable|array',
            'bank_accounts.*.id' => 'nullable|exists:mitra_banks,id',
            'bank_accounts.*.bank_id' => 'nullable|exists:banks,id',
            'bank_accounts.*.rek_no' => 'nullable|string|max:50',
            'bank_accounts.*.rek_name' => 'nullable|string|max:255',
            'bank_accounts.*.is_default' => 'nullable|boolean',
            'deleted_bank_accounts' => 'nullable|array',
            'deleted_bank_accounts.*' => 'exists:mitra_banks,id',
        ];
        $mitra = Mitra::findOrFail($id);
        if ($request->has('create_account')) {
            if (!$mitra->user_id) {
                $rules = array_merge($rules, [
                    'email' => 'required|email|max:255|unique:users,email',
                    'password' => 'required|min:8',
                    'password_confirmation' => 'required|same:password',
                ]);
            } else {
                if ($request->filled('password')) {
                    $rules = array_merge($rules, [
                        'password' => 'min:8',
                        'password_confirmation' => 'required|same:password',
                    ]);
                }
            }
        }
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try {
            DB::beginTransaction();
            
            $mitraData = $request->except(['country', 'password', 'password_confirmation', 'create_account', 'bank_accounts', 'deleted_bank_accounts', 'ktp']);
       
            
            if ($request->hasFile('ktp')) {
                if ($mitra->ktp && file_exists(public_path('ktp/' . $mitra->ktp))) {
                    unlink(public_path('ktp/' . $mitra->ktp));
                }
                
                $file = $request->file('ktp');
                $filename = time() . '_' . $file->getClientOriginalName();
                
                if (!File::exists(public_path('ktp'))) {
                    File::makeDirectory(public_path('ktp'), 0777, true);
                }
                
                $file->move(public_path('ktp'), $filename);
                $mitraData['ktp'] = $filename;
            }
            
            $mitra->update($mitraData);
            
            if ($request->has('country')) {
                CountryMitra::where('mitra_id', $mitra->id)->delete();
                
                if (is_array($request->country)) {
                    foreach ($request->country as $countryName) {
                        CountryMitra::create([
                            'mitra_id' => $mitra->id,
                            'name' => $countryName,
                        ]);
                    }
                }
            }
                
            if ($request->has('bank_accounts') && is_array($request->bank_accounts)) {
                MitraBank::where('mitra_id', $mitra->id)->delete();
                
                foreach ($request->bank_accounts as $bankAccountId) {
                    MitraBank::create([
                        'mitra_id' => $mitra->id,
                        'bank_id' => $bankAccountId['bank_id'] ?? null,
                        'rek_no' => $bankAccountId['rek_no'] ?? null,
                        'rek_name' => $bankAccountId['rek_name'] ?? null,
                        'is_default' => $bankAccountId['is_default'] ?? false,
                    ]);
                }
            }
      
            // Handle user account
            if ($request->has('create_account')) {
                if ($mitra->user_id) {
                    $user = User::findOrFail($mitra->user_id);
                    
                    $userData = [
                        'name' => $request->name,
                        'email' => $request->email,
                    ];
                    
                    if ($request->filled('password')) {
                        $userData['password'] = Hash::make($request->password);
                    }
                    
                    $user->update($userData);
                    
                } else {
                    $user = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => Hash::make($request->password),
                        'phone' => $request->phone1,
                        'status' => UserStatus::ACTIVE,
                    ]);
                    
                    $user->assignRole('mitra');
                    $mitra->update(['user_id' => $user->id]);
                }
            } else if ($mitra->user_id) {
                $mitra->update(['user_id' => null]);
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Mitra updated successfully!');
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
            if ($mitra->ktp && file_exists(public_path('ktp/' . $mitra->ktp))) {
                unlink(public_path('ktp/' . $mitra->ktp));
            }
            
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

    /**
     * Export the list of mitras.
     */
    public function export(Request $request)
    {
        $search = $request->query('search');
        $groupId = $request->query('group_id');
        
        $timestamp = now()->format('Y-m-d-His');
        $filename = "mitras-{$timestamp}.xlsx";
        
        return Excel::download(new MitrasExport($search, $groupId), $filename);
    }
   
}