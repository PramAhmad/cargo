<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Tax List" page="Tax Management" />
    <!-- Page Title Ends -->

    <!-- Tax List Starts -->
    <div class="space-y-4">
        <!-- Tax Header Starts -->
        <div class="flex flex-col items-center justify-between gap-y-4 md:flex-row md:gap-y-0">
            <div class="flex w-full flex-col md:flex-row gap-2 md:gap-3 md:w-auto">
                <!-- Tax Search Starts -->
                <form
                    action="{{ route('taxes.index') }}"
                    method="GET"
                    class="group flex h-10 w-full items-center rounded-primary border border-transparent bg-white shadow-sm focus-within:border-primary-500 focus-within:ring-1 focus-within:ring-inset focus-within:ring-primary-500 dark:border-transparent dark:bg-slate-800 dark:focus-within:border-primary-500 md:w-72">
                    <div class="flex h-full items-center px-2">
                        <i class="h-4 text-slate-400 group-focus-within:text-primary-500" data-feather="search"></i>
                    </div>
                    <input
                        class="h-full w-full border-transparent bg-transparent px-0 text-sm placeholder-slate-400 placeholder:text-sm focus:border-transparent focus:outline-none focus:ring-0"
                        type="text"
                        name="search"
                        value="{{ $search ?? '' }}"
                        placeholder="Search by tax name" />
                    @if(isset($search) && !empty($search))
                        <div class="flex h-full items-center px-2">
                            <a href="{{ route('taxes.index') }}" class="text-slate-400 hover:text-danger-500">
                                <i class="h-4" data-feather="x-circle"></i>
                            </a>
                        </div>
                    @endif
                </form>
                <!-- Tax Search Ends -->
            </div>
        </div>
        <!-- Tax Header Ends -->

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif

        <!-- Tax Table Starts -->
        <div class="table-responsive whitespace-nowrap rounded-primary">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-[5%] uppercase">ID</th>
                        <th class="w-[25%] uppercase">Name</th>
                        <th class="w-[20%] uppercase">Type</th>
                        <th class="w-[25%] uppercase">Value</th>
                        <th class="w-[15%] uppercase text-center">Status</th>
                        <th class="w-[10%] text-center uppercase">Save</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($taxes as $tax)
                    <tr class="tax-row" data-id="{{ $tax->id }}">
                        <td>{{ $tax->id }}</td>
                        <td>{{ $tax->name }}</td>
                        <td>
                            <div class="relative">
                                <select class="tax-type select select-sm w-full" data-original="{{ $tax->type }}">
                                    <option value="percentage" {{ $tax->type == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    <option value="fixed" {{ $tax->type == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="relative">
                                <input type="text" class="tax-value input input-sm w-full" value="{{ $tax->value }}" data-original="{{ $tax->value }}">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="tax-suffix {{ $tax->type == 'percentage' ? '' : 'hidden' }}">%</span>
                                    <span class="tax-prefix {{ $tax->type == 'fixed' ? '' : 'hidden' }}">Rp</span>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="tax-active sr-only peer" {{ $tax->is_active ? 'checked' : '' }} data-original="{{ $tax->is_active ? '1' : '0' }}">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-primary-500"></div>
                            </label>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn-save btn btn-sm btn-primary opacity-0 invisible transition-opacity duration-200" disabled>
                                <i class="h-4 w-4" data-feather="save"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            @if(isset($search) && !empty($search))
                                No taxes found matching your search criteria.
                            @else
                                No taxes available.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Tax Table Ends -->

        <!-- Pagination Starts -->
        @if($taxes->hasPages())
        <div class="flex flex-col items-center justify-between gap-y-4 md:flex-row">
            <p class="text-xs font-normal text-slate-400">
                Showing {{ $taxes->firstItem() ?? 0 }} to {{ $taxes->lastItem() ?? 0 }} of {{ $taxes->total() ?? 0 }} results
            </p>
            
            {{ $taxes->links() }}
        </div>
        @endif
        <!-- Pagination Ends -->
    </div>
    <!-- Tax List Ends -->

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Feather Icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
            
            // Helper function to check if any changes were made to a row
            function checkRowChanges(row) {
                const typeSelect = row.querySelector('.tax-type');
                const valueInput = row.querySelector('.tax-value');
                const activeToggle = row.querySelector('.tax-active');
                const saveButton = row.querySelector('.btn-save');
                
                const typeChanged = typeSelect.value !== typeSelect.getAttribute('data-original');
                const valueChanged = valueInput.value !== valueInput.getAttribute('data-original');
                const activeChanged = (activeToggle.checked ? '1' : '0') !== activeToggle.getAttribute('data-original');
                
                if (typeChanged || valueChanged || activeChanged) {
                    saveButton.classList.remove('opacity-0', 'invisible');
                    saveButton.classList.add('opacity-100', 'visible');
                    saveButton.disabled = false;
                } else {
                    saveButton.classList.add('opacity-0', 'invisible');
                    saveButton.classList.remove('opacity-100', 'visible');
                    saveButton.disabled = true;
                }
                
                // Update value field suffix/prefix based on type
                const suffix = row.querySelector('.tax-suffix');
                const prefix = row.querySelector('.tax-prefix');
                
                if (typeSelect.value === 'percentage') {
                    suffix.classList.remove('hidden');
                    prefix.classList.add('hidden');
                } else {
                    suffix.classList.add('hidden');
                    prefix.classList.remove('hidden');
                }
            }
            
            // Add event listeners to all editable fields
            document.querySelectorAll('.tax-row').forEach(row => {
                const typeSelect = row.querySelector('.tax-type');
                const valueInput = row.querySelector('.tax-value');
                const activeToggle = row.querySelector('.tax-active');
                const saveButton = row.querySelector('.btn-save');
                
                // Monitor changes
                typeSelect.addEventListener('change', () => checkRowChanges(row));
                valueInput.addEventListener('input', () => checkRowChanges(row));
                activeToggle.addEventListener('change', () => checkRowChanges(row));
                
                // Handle save button click
                saveButton.addEventListener('click', function() {
                    const taxId = row.getAttribute('data-id');
                    const type = typeSelect.value;
                    const value = valueInput.value;
                    const isActive = activeToggle.checked ? 1 : 0;
                    
                    // Show loading state
                    saveButton.disabled = true;
                    saveButton.innerHTML = '<span class="animate-spin inline-block h-4 w-4 border-2 border-current border-t-transparent rounded-full" aria-hidden="true"></span>';
                    
                    // Send AJAX request to update the tax
                    fetch(`/taxes/${taxId}/update-fields`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ type, value, is_active: isActive })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update original values
                            typeSelect.setAttribute('data-original', type);
                            valueInput.setAttribute('data-original', value);
                            activeToggle.setAttribute('data-original', isActive.toString());
                            
                            // Show success message
                            const alertContainer = document.createElement('div');
                            alertContainer.className = 'alert alert-success mt-4';
                            alertContainer.textContent = data.message || 'Tax updated successfully';
                            
                            const spaceY4 = document.querySelector('.space-y-4');
                            spaceY4.insertBefore(alertContainer, spaceY4.firstChild);
                            
                            // Remove the alert after 3 seconds
                            setTimeout(() => {
                                alertContainer.remove();
                            }, 3000);
                            
                            // Reset save button
                            saveButton.innerHTML = '<i class="h-4 w-4" data-feather="save"></i>';
                            feather.replace();
                            checkRowChanges(row);
                        } else {
                            throw new Error(data.message || 'Failed to update tax');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        
                        // Show error message
                        const alertContainer = document.createElement('div');
                        alertContainer.className = 'alert alert-danger mt-4';
                        alertContainer.textContent = error.message || 'An error occurred';
                        
                        const spaceY4 = document.querySelector('.space-y-4');
                        spaceY4.insertBefore(alertContainer, spaceY4.firstChild);
                        
                        // Remove the alert after 5 seconds
                        setTimeout(() => {
                            alertContainer.remove();
                        }, 5000);
                        
                        // Reset save button
                        saveButton.innerHTML = '<i class="h-4 w-4" data-feather="save"></i>';
                        feather.replace();
                        saveButton.disabled = false;
                    });
                });
            });
        });
    </script>
    @endpush
</x-app-layout>