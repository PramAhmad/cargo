<x-app-layout>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>Bank Details</h2>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <strong>ID:</strong>
                        {{ $bank->id }}
                    </div>
                    <div class="form-group">
                        <strong>Name:</strong>
                        {{ $bank->name }}
                    </div>
                    <div class="form-group">
                        <strong>Created At:</strong>
                        {{ $bank->created_at }}
                    </div>
                    <div class="form-group">
                        <strong>Updated At:</strong>
                        {{ $bank->updated_at }}
                    </div>
                    <div class="form-group mt-3">
                        <a class="btn btn-primary" href="{{ route('banks.index') }}">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>