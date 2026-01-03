@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <h1 class="display-1 fw-bold text-danger">401</h1>
                    </div>
                    
                    <h2 class="card-title mb-3">{{ $type ?? 'Unauthenticated' }}</h2>
                    
                    <p class="card-text text-muted mb-4">
                        {{ $message ?? 'Anda harus login terlebih dahulu untuk mengakses halaman ini.' }}
                    </p>

                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4 gap-3">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg px-4">
                            <i class="bi bi-house"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
