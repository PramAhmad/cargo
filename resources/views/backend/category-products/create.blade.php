<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Add Category Product" page="Products" />
    <!-- Page Title Ends -->

    <div class="space-y-6">
        <div class="card">
            <div class="card-body p-6">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('category-products.store') }}" method="POST">
                    @csrf
                    <div class="grid w-full grid-cols-1 gap-4 py-2 md:grid-cols-2">
                        <div class="flex w-full flex-col md:w-auto">
                            <label class="label label-required mb-1 font-medium" for="name">Category Product Name</label>
                            <input type="text" class="input" id="name" name="name" value="{{ old('name') }}" />
                        </div>
                        
                        <div class="flex w-full flex-col md:w-auto">
                            <label class="label label-required mb-1 font-medium" for="mitra_id">Mitra</label>
                            <select class="select" id="mitra_id" name="mitra_id">
                                <option value="">Select Mitra</option>
                                @foreach($mitras as $mitra)
                                    <option value="{{ $mitra->id }}" {{ old('mitra_id') == $mitra->id ? 'selected' : '' }}>
                                        {{ $mitra->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h3 class="font-medium text-lg mb-3">Pricing Information</h3>
                        <div class="grid w-full grid-cols-1 gap-4 py-2 md:grid-cols-2">
                            <div class="flex w-full flex-col md:w-auto">
                                <label class="label mb-1 font-medium" for="mit_price_cbm">Mitra Price (CBM)</label>
                                <div class="relative">
                                 
                                    <input type="text" class="input pl-10" id="mit_price_cbm" name="mit_price_cbm" 
                                            step="0.01" value="{{ old('mit_price_cbm') }}" />
                                </div>
                            </div>
                            
                            <div class="flex w-full flex-col md:w-auto">
                                <label class="label mb-1 font-medium" for="mit_price_kg">Mitra Price (KG)</label>
                                <div class="relative">
                                 
                                    <input type="text" class="input pl-10" id="mit_price_kg" name="mit_price_kg" 
                                            step="0.01" value="{{ old('mit_price_kg') }}" />
                                </div>
                            </div>
                            
                            <div class="flex w-full flex-col md:w-auto">
                                <label class="label mb-1 font-medium" for="cust_price_cbm">Customer Price (CBM)</label>
                                <div class="relative">
                                 
                                    <input type="text" class="input pl-10" id="cust_price_cbm" name="cust_price_cbm" 
                                            step="0.01" value="{{ old('cust_price_cbm') }}" />
                                </div>
                            </div>
                            
                            <div class="flex w-full flex-col md:w-auto">
                                <label class="label mb-1 font-medium" for="cust_price_kg">Customer Price (KG)</label>
                                <div class="relative">
                                 
                                    <input type="text" class="input pl-10" id="cust_price_kg" name="cust_price_kg" 
                                            step="0.01" value="{{ old('cust_price_kg') }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex items-center justify-end gap-4">
                        <a href="{{ route('category-products.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
<script>
    let id = [
        'mit_price_cbm',
        'mit_price_kg',
        'cust_price_cbm',
        'cust_price_kg'
    ]
    for (let i = 0; i < id.length; i++) {
        let cleave = new Cleave('#' + id[i], {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalMark: ',',
            delimiter: '.'
        });
    }
</script>
@endpush
</x-app-layout>