@extends('layouts.app')

@section('title', 'Recuperar Contraseña')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">{{ __('Recuperar Contraseña') }}</h4>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Correo Electrónico') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Enviar Link de Recuperación') }}
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card-footer text-center">
                    <a href="{{ route('login') }}" class="text-primary">
                        <i class="bi bi-arrow-left"></i> Volver al inicio de sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
