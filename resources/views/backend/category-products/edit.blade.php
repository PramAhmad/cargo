<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Edit Category Product" page="Products" />
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
                
                <form action="{{ route('category-products.update', $categoryProduct->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid w-full grid-cols-1 gap-4 py-2 md:grid-cols-2">
                        <div class="flex w-full flex-col md:w-auto">
                            <label class="label label-required mb-1 font-medium" for="name">Category Product Name</label>
                            <input type="text" class="input" id="name" name="name" value="{{ old('name', $categoryProduct->name) }}" />
                        </div>
                        
                        <div class="flex w-full flex-col md:w-auto">
                            <label class="label label-required mb-1 font-medium" for="mitra_id">Mitra</label>
                            <select class="select" id="mitra_id" name="mitra_id">
                                <option value="">Select Mitra</option>
                                @foreach($mitras as $mitra)
                                    <option value="{{ $mitra->id }}" 
                                            {{ old('mitra_id', $categoryProduct->mitra_id) == $mitra->id ? 'selected' : '' }}>
                                        {{ $mitra->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Pricing Information Section -->
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-slate-800 dark:text-slate-200 mb-4">Pricing Information</h3>
                        
                        <div class="grid w-full grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- CBM Pricing -->
                            <div class="border dark:border-slate-600 rounded-lg p-4">
                                <h4 class="font-medium text-slate-700 dark:text-slate-300 mb-3">CBM Pricing</h4>
                                <div class="space-y-4">
                                    <div class="flex w-full flex-col">
                                        <label class="label mb-1 font-medium" for="mit_price_cbm">Mitra Price</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                                            <input type="number" class="input pl-10" id="mit_price_cbm" name="mit_price_cbm" 
                                                min="0" step="0.01" value="{{ old('mit_price_cbm', $categoryProduct->mit_price_cbm) }}" />
                                        </div>
                                    </div>
                                    
                                    <div class="flex w-full flex-col">
                                        <label class="label mb-1 font-medium" for="cust_price_cbm">Customer Price</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                                            <input type="number" class="input pl-10" id="cust_price_cbm" name="cust_price_cbm" 
                                                min="0" step="0.01" value="{{ old('cust_price_cbm', $categoryProduct->cust_price_cbm) }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- KG Pricing -->
                            <div class="border dark:border-slate-600 rounded-lg p-4">
                                <h4 class="font-medium text-slate-700 dark:text-slate-300 mb-3">KG Pricing</h4>
                                <div class="space-y-4">
                                    <div class="flex w-full flex-col">
                                        <label class="label mb-1 font-medium" for="mit_price_kg">Mitra Price</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                                            <input type="number" class="input pl-10" id="mit_price_kg" name="mit_price_kg" 
                                                min="0" step="0.01" value="{{ old('mit_price_kg', $categoryProduct->mit_price_kg) }}" />
                                        </div>
                                    </div>
                                    
                                    <div class="flex w-full flex-col">
                                        <label class="label mb-1 font-medium" for="cust_price_kg">Customer Price</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                                            <input type="number" class="input pl-10" id="cust_price_kg" name="cust_price_kg" 
                                                min="0" step="0.01" value="{{ old('cust_price_kg', $categoryProduct->cust_price_kg) }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex items-center justify-end gap-4">
                        <a href="{{ route('category-products.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>