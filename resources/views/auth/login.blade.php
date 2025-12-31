@extends('layouts.app')
@section('body-attribute ')
    class="hold-transition login-page"
@endsection
@section('content')
    <div class="login-box">
        <div class="login-logo">
            <img src="{{asset('homepage/assets/img/logo.png')}}" alt="logo" style="width:100px;" class="mr-2"> 

            <span><b>Admin </b>Login</span>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Log in to begin your session</p>

                <form method="POST" action="{{ route('login') }}" id="login-form">
                    @csrf

                    <div class="input-group mb-3">
                        <input id="email" type="email" class="form-control  @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" autocomplete="email" autofocus placeholder="Email" onchange="trimInput(this)">
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
                            name="password" autocomplete="current-password" placeholder="Password"  onchange="trimInput(this)">
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

                    <div class="row">
                        <div class="col-4">
                            <span>Number: </span><span id="captchaNumber"></span>
                        </div>
                        <div class="input-group col-12">
                            <input type="number" placeholder="Enter above number" class="form-control" min="0"
                                id="captchaInput" name="captchaInput" required oninput="this.value = this.value.slice(0, 4);">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="generateCaptcha()">Generate
                                        New</button>
                                </div>
                        </div>
                        {{-- <div class="col-8 mt-3">
                            <div class="icheck-primary">
                                <input type="checkbox" name="remember" id="remember"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div> --}}
                        <!-- /.col -->
                        <div class="col-4 mt-3">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
                <!-- /.social-auth-links -->
                @if (Route::has('password.request'))
                    <p class="mb-1">
                        <a href="{{ route('password.request') }}"> {{ __('Forgot Your Password?') }}</a>
                    </p>
                @endif

               <!--  <p class="mb-0">
                    <a href="" class="text-center">Register Authorized Person</a>
                </p>
                <p class="mb-0">
                    <a href="" class="text-center">Register Company</a>
                </p> -->
                <!-- <p class="mb-0">
                    <a href="" class="text-center">Voter Login</a>
                </p> -->
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>

@endsection
@section('footer-script')
<script>
     function trimInput(input) {
        input.value = input.value.trim();
    }
    // Function to generate a random number between 1000 and 9999
    function generateRandomNumber() {
        return Math.floor(Math.random() * 9000) + 1000;
    }

    // Function to generate a new CAPTCHA
    function generateCaptcha() {
        var captchaNumberElement = document.getElementById('captchaNumber');
        var randomCaptchaNumber = generateRandomNumber();
        captchaNumberElement.textContent = randomCaptchaNumber;
    }

    // Initial generation of CAPTCHA on page load
    generateCaptcha();

    // Event listener for form submission
    document.getElementById('login-form').addEventListener('submit', function(event) {
        var userInput = document.getElementById('captchaInput').value;
        var captchaNumber = document.getElementById('captchaNumber').textContent;

        if (userInput !== captchaNumber) {
            createMessage("CAPTCHA verification failed. Please try again.","error")
            event.preventDefault(); // Prevent the form from being submitted
            generateCaptcha(); // Generate a new CAPTCHA
        } else {
            // Continue with form submission or other actions
        }
    });
</script>
@endsection
