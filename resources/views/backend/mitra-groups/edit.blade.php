<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Edit Mitra Group" page="Partners" />
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
                
                <form action="{{ route('mitra-groups.update', $mitraGroup->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid w-full grid-cols-1 gap-4 py-2 md:grid-cols-2">
                        <div class="flex w-full flex-col md:w-auto">
                            <label class="label label-required mb-1 font-medium" for="name">Mitra Group Name</label>
                            <input type="text" class="input" id="name" name="name" value="{{ $mitraGroup->name }}" />
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-4">
                        <a href="{{ route('mitra-groups.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>