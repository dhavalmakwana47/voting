@extends('app.layout.app')
@section('page_title')
    User Add
@endsection
@section('header-script')
@endsection
@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        User Details {{ isset($user) ? 'Edit' : 'Add' }}
                    </h3>
                </div>
                {{-- Data insert and update in one file --}}
                <form action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}" method="post">
                    @csrf
                    @if (isset($user))
                    @method('PATCH')

                    <input type="hidden" value="{{ $user->id }}" name="id">
 
                    @endif
                    <div class="card-body">
                        <div class="row">
                            <div class="input-group mb-3">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                    name="name" value="{{ isset($user) ? $user->name : old('name') }}" autocomplete="name" autofocus
                                    placeholder="Full name">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-user"></span>
                                    </div>
                                </div>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
        
                            <div class="input-group mb-3">
                                <input id="pan_number" type="text" class="form-control @error('pan_number') is-invalid @enderror"
                                    name="pan_number" value="{{ isset($user) ? $user->pan_number : old('pan_number') }}" autocomplete="pan_number" autofocus
                                    placeholder="Pan Number">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fa fa-id-card"></span>
                                    </div>
                                </div>
                                @error('pan_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
        
                            <div class="input-group mb-3">
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror"
                                    name="phone" value="{{ isset($user) ? $user->phone : old('phone') }}" autocomplete="phone" autofocus
                                    placeholder="Phone Number">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-phone"></span>
                                    </div>
                                </div>
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
        
                            <div class="input-group mb-3">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ isset($user) ? $user->email : old('email') }}" autocomplete="email" placeholder="Email">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-envelope"></span>
                                    </div>
                                </div>
        
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="input-group mb-3">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                    name="password" autocomplete="new-password" placeholder="Password">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="input-group mb-3">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                                    autocomplete="new-password" placeholder="Confirmed Password">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                            </div>
        
                            <div class="input-group mb-3">
                                {{-- <div class="form-group"> --}}
        
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="1" name="user_type" {{ ( isset($user) && $user->user_type) == "1" ? 'checked' : ''}} {{ empty(old('user_type')) ?'checked' : ''  }}>
                                        <label class="form-check-label">AR</label>
                                    </div>&nbsp;&nbsp;
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="2" name="user_type" {{ isset($user) && $user->user_type == "2" ? 'checked' : ''}} >
                                        <label class="form-check-label">Scrutinizer</label>
                                    </div>
                                {{-- </div> --}}
                                  @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div>
                                <label for="resolution_meetingdate">Active Status</label>

                                <div>
                                    <input type="checkbox" data-bootstrap-switch="" name="is_active"
                                        {{ (isset($user) && $user->is_active) ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit"  name="submit" class="btn btn-primary">
                            Save
                        </button>
                        &nbsp;
                    </div>
                </form>

            </div>
        </div>
    </div>
    </div>
@endsection
@section('footer-script')
    <script>
        $("input[data-bootstrap-switch]").each(function() {
            $(this).bootstrapSwitch();
        })
    </script>
@endsection
