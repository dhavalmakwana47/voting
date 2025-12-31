@extends('layouts.app')

@section('body-attribute')
    class="hold-transition register-page"
@endsection

@section('content')
<div class="d-flex justify-content-center align-items-center min-vh-100 bg-light"> <!-- Full height, centered content with light background -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6"> <!-- Slimmer column for better focus on the form -->
                <div class="card shadow-lg border-0 rounded-lg"> <!-- Shadow and rounded borders for an elevated look -->
                    <div class="card-header bg-primary text-white text-center rounded-top"> <!-- Styled header with primary background and white text -->
                        <h4 class="mb-0">{{ __('Reset Password') }}</h4>
                    </div>

                    <div class="card-body p-5"> <!-- Increased padding for spacious feel -->
                        @if (session('status'))
                            <div class="alert alert-success text-center" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="email" class="form-label">{{ __('Email Address') }}</label> <!-- More modern form label -->
                                <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Enter your email"> <!-- Larger input with placeholder -->

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="d-grid"> <!-- Full-width button using d-grid for modern layout -->
                                <button type="submit" class="btn btn-primary btn-lg"> <!-- Larger button for better accessibility -->
                                    {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer text-center py-3 bg-light rounded-bottom"> <!-- Subtle footer with background -->
                        <small class="text-muted">Remember your password? <a href="{{ route('login') }}" class="text-primary">Login here</a></small> <!-- Link to login for improved UX -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
