
@extends('app.layout.app')
@section('page_title')
Change Password
@endsection
@section('header-script')
@endsection
@section('content-body')
<div class="container">
    <h2>Change Password</h2>
    
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('userpassword.update') }}">
        @csrf
        
        <div class="form-group">
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="new_password_confirmation">Confirm New Password</label>
            <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Change Password</button>
    </form>
</div>
@endsection
