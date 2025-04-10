
<x-app-layout>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>Edit Bank</h2>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                @endif
                    
                    <form action="{{ route('banks.update', $bank->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid w-full grid-cols-1 gap-4 py-2 md:grid-cols-2">
                            <div class="flex w-full flex-col md:w-auto">
                                <label class="label label-required mb-1 font-medium" for="name"> Name Bank </label>
                                <input type="text" class="input" id="name" name="name" value="{{ $bank->name }}" />
                            </div>
                        </div>
                        <div class="mt-6 flex items-center justify-end gap-4">
                        <a href="{{ route('customer-groups.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>