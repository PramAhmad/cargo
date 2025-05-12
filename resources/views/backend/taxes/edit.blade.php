<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Edit Tax" page="Tax Management">
        <li>
            <a href="{{ route('taxes.index') }}">Taxes</a>
        </li>
        <li class="current">Edit</li>
    </x-page-title>
    <!-- Page Title Ends -->

    <!-- Edit Tax Form Starts -->
    <div class="space-y-4">
        <div class="card">
            <div class="card-body p-6">
                <form action="{{ route('taxes.update', $tax->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <!-- Tax Name Field -->
                        <div>
                            <label for="name" class="label label-required mb-1 text-sm font-medium">Tax Name</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="input"
                                placeholder="Enter tax name"
                                value="{{ old('name', $tax->name) }}"
                                required
                            />
                            @error('name')
                                <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tax Type Field -->
                        <div>
                            <label for="type" class="label label-required mb-1 text-sm font-medium">Tax Type</label>
                            <select id="type" name="type" class="select" required>
                                <option value="">Select Type</option>
                                @foreach($taxTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('type', $tax->type) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tax Value Field -->
                        <div>
                            <label for="value" class="label label-required mb-1 text-sm font-medium">
                                <span id="value-label">Tax Value</span>
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    id="value"
                                    name="value"
                                    class="input"
                                    placeholder="Enter tax value"
                                    step="0.01"
                                    min="0"
                                    value="{{ old('value', $tax->value) }}"
                                    required
                                />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span id="value-suffix" class="text-gray-500"></span>
                                </div>
                            </div>
                            @error('value')
                                <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tax Status Field -->
                        <div>
                            <div class="flex items-center mt-6">
                                <input
                                    type="checkbox"
                                    id="is_active"
                                    name="is_active"
                                    class="checkbox"
                                    value="1"
                                    {{ old('is_active', $tax->is_active) ? 'checked' : '' }}
                                />
                                <label for="is_active" class="ml-2 text-sm font-medium">Active</label>
                            </div>
                            @error('is_active')
                                <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('taxes.index') }}" class="btn btn-secondary mr-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Tax</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Tax Form Ends -->

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const valueLabel = document.getElementById('value-label');
            const valueSuffix = document.getElementById('value-suffix');
            
            function updateValueField() {
                if (typeSelect.value === 'percentage') {
                    valueLabel.textContent = 'Tax Percentage';
                    valueSuffix.textContent = '%';
                } else {
                    valueLabel.textContent = 'Tax Amount';
                    valueSuffix.textContent = '';
                }
            }
            
            // Initial update
            updateValueField();
            
            // Update on change
            typeSelect.addEventListener('change', updateValueField);
        });
    </script>
    @endpush
</x-app-layout>