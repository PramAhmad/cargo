<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Marketing Group Details" page="Marketing" />
    <!-- Page Title Ends -->

    <div class="space-y-6">
        <div class="card">
            <div class="card-body p-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="flex flex-col gap-y-2">
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-300">ID</span>
                        <span class="text-base">{{ $marketingGroup->id }}</span>
                    </div>
                    
                    <div class="flex flex-col gap-y-2">
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-300">Name</span>
                        <span class="text-base">{{ $marketingGroup->name }}</span>
                    </div>
                    
                    <div class="flex flex-col gap-y-2">
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-300">Created At</span>
                        <span class="text-base">{{ $marketingGroup->created_at->format('d M Y H:i:s') }}</span>
                    </div>
                    
                    <div class="flex flex-col gap-y-2">
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-300">Updated At</span>
                        <span class="text-base">{{ $marketingGroup->updated_at->format('d M Y H:i:s') }}</span>
                    </div>
                </div>
                
                <div class="mt-6 flex items-center justify-end gap-4">
                    <a href="{{ route('marketing-groups.index') }}" class="btn btn-secondary">Back</a>
                    <a href="{{ route('marketing-groups.edit', $marketingGroup->id) }}" class="btn btn-primary">Edit</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>