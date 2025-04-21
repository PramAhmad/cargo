<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Edit Warehouse" page="Mitras" />
    <!-- Page Title Ends -->

    <div class="space-y-6">
        <div class="card">
            <div class="card-body p-6">
                <form action="{{ route('mitra.warehouses.update', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="flex items-center p-4 mb-6 rounded-md bg-slate-50 dark:bg-slate-800">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-500 text-white">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="ml-4">
                            <h5 class="text-base font-medium">{{ $mitra->name }}</h5>
                            <p class="text-sm text-slate-500">{{ $mitra->code }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Warehouse Name -->
                        <div class="flex flex-col gap-1">
                            <label class="label label-required mb-1 font-medium" for="name">Warehouse Name</label>
                            <input type="text" class="input" id="name" name="name" value="{{ old('name', $warehouse->name) }}" required />
                        </div>
                        
                        <!-- Warehouse Type -->
                        <div class="flex flex-col gap-1">
                            <label class="label label-required mb-1 font-medium" for="type">Warehouse Type</label>
                            <select id="type" name="type" class="select" required>
                                <option value="sea" {{ old('type', $warehouse->type) == 'sea' ? 'selected' : '' }}>Sea Freight</option>
                                <option value="air" {{ old('type', $warehouse->type) == 'air' ? 'selected' : '' }}>Air Freight</option>
                            </select>
                        </div>
                        
                        <!-- Warehouse Address -->
                        <div class="flex flex-col gap-1 md:col-span-2">
                            <label class="label mb-1 font-medium" for="address">Warehouse Address</label>
                            <textarea class="textarea" id="address" name="address" rows="3">{{ old('address', $warehouse->address) }}</textarea>
                        </div>
                        
                        <!-- Address Photo -->
                        <div class="flex flex-col gap-1 md:col-span-2">
                            <label class="label mb-1 font-medium" for="address_photo">Address Photo</label>
                            <input type="file" class="input" id="address_photo" name="address_photo" accept="image/*" />
                            
                            @if($warehouse->address_photo)
                                <img src="{{ asset($warehouse->address_photo) }}" class="max-w-[300px]" alt="Warehouse Image">
                            @endif
                            
                            <p class="text-xs text-slate-500 mt-1">Upload an image of the warehouse location (JPEG, PNG, JPG, max 2MB)</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex items-center justify-end border-t pt-4">
                        <a href="{{ route('mitras.edit', $mitra->id) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary ml-2">Update Warehouse</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>