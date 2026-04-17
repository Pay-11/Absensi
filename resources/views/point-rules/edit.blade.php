@extends('layouts.app')

@section('title', 'Edit Point Rule')

@section('content')
    <div class="container-xxl py-4">
        <div class="card shadow-sm mx-auto" style="max-width: 600px;">
            <div class="card-header bg-white">
                <h5 class="mb-0">Edit Point Rule</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('point-rules.update', $pointRule->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('point-rules.form', ['model' => $pointRule])

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('point-rules.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-warning">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Fallback Bootstrap CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection