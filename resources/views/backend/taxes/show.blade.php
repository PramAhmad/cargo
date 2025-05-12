<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Tax Details" page="Tax Management">
        <li>
            <a href="{{ route('taxes.index') }}">Taxes</a>
        </li>
        <li class="current">Details</li>
    </x-page-title>
    <!-- Page Title Ends -->

    <!-- Tax Details Starts -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium text-slate-900 dark:text-slate-100">
                {{ $tax->name }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('taxes.edit', $tax->id) }}" class="btn btn-primary btn-sm">
                    <i class="h-5 w-5" data-feather="edit"></i>
                    <span>Edit</span>
                </a>
                <form action="{{ route('taxes.destroy', $tax->id) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this tax?')">
                        <i class="h-5 w-5" data-feather="trash"></i>
                        <span>Delete</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400">Tax Name</h4>
                            <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-100">{{ $tax->name }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400">Type</h4>
                            <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-100 capitalize">
                                {{ $tax->type }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400">Value</h4>
                            <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-100">
                                @if($tax->type == 'percentage')
                                    {{ $tax->value }}%
                                @else
                                    {{ number_format($tax->value, 0, ',', '.') }}
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400">Status</h4>
                            <p class="mt-1">
                                @if($tax->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400">Created At</h4>
                            <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-100">
                                {{ $tax->created_at->format('d M Y, h:i A') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Tax Details Ends -->
</x-app-layout>