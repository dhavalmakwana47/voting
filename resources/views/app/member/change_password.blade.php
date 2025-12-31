@extends('app.member.layout')
@section('header-script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('customdownload/css/jquery.dataTables2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('customdownload/css/jquery.dataTables.min.css') }}">
@endsection
@section('body-content')
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">{{ __('Change Password') }}</div>
        
                        <div class="card-body">
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif
        
                            <form method="POST" action="{{ route('member.update_password') }}">
                                @csrf
        

        
                                <div class="form-group row mt-3">
                                    <label for="new_password" class="col-md-4 col-form-label text-md-right">{{ __('New Password') }}</label>
        
                                    <div class="col-md-6">
                                        <input id="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror" name="new_password" required>
        
                                        @error('new_password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
        
                                <div class="form-group row mt-3">
                                    <label for="new_password_confirmation" class="col-md-4 col-form-label text-md-right">{{ __('Confirm New Password') }}</label>
        
                                    <div class="col-md-6">
                                        <input id="new_password_confirmation" type="password" class="form-control" name="new_password_confirmation" required>
                                    </div>
                                </div>
        
                                <div class="form-group row mt-3 mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Change Password') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('footer-script')
@endsection
