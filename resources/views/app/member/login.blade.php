<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('/dist/css/adminlte.min.css') }}">
    <link href="{{ asset('customdownload/css/toastr.min.css') }}" rel="stylesheet">
    <style>
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        #loader {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 9999;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: hidden;
            /* Disable scroll */
            background: rgba(0, 0, 0, 0.6);
            /* Black background with transparency */
            text-align: center;
            /* Center the loading image */
            align-items: center;
            /* Center the content vertically */
            justify-content: center;
            /* Center the content horizontally */
        }

        #loader img {
            margin-top: 20%;
            max-width: 100px;
            /* Adjust the size of the image */
        }

        #loader p {
            color: white;
            /* White text color */
            font-size: 20px;
            /* Text size */
            margin-top: 10px;
            /* Space between image and text */
        }
    </style>
</head>

<body class="hold-transition login-page">
    <div id="loader" style="display:none;">
        <img src="{{ asset('loading-4802_256.gif') }}" alt="Loading...">
        <p id="loader-text"></p>
    </div>
    <div class="login-box">
        <div class="login-logo">
            <img src="{{ asset('homepage/assets/img/logo.png') }}" alt="logo" style="width:100px;" class="mr-2">
            <span><b>Voter </b>Login</span>

        </div>
        <!-- /.login-logo -->

        <div class="card">
            <div class="card-body login-card-body">
                <!-- <div class="col-12" >
                    <a href="{{ route('login') }}" class="btn btn-primary btn-block" >Back To Admin  Login</a>
                </div> -->
                <p class="login-box-msg">Log in to begin your session</p>
                {{ session('member_login') }}
                <form method="POST" action="{{ route('member.loginvalidate') }}" id="member-login-form">
                    @csrf
                    <div class="input-group mb-3" id="login-type-div">
                        <div class="form-check">
                            <input class="form-check-input login-type-radio" onclick="changeLoginType(1)"
                                value="user_id" type="radio" name="login_type" checked>
                            <label class="form-check-label">User ID</label>
                        </div>&nbsp;
                        <div class="form-check">
                            <input class="form-check-input login-type-radio" onclick="changeLoginType(2)" value="email"
                                type="radio" name="login_type">
                            <label class="form-check-label">Email</label>
                        </div>&nbsp;
                        <div class="form-check">
                            <input class="form-check-input login-type-radio" onclick="changeLoginType(3)" value="phone"
                                type="radio" name="login_type">
                            <label class="form-check-label">Phone</label>
                        </div>
                    </div>

                    <div class="form-group mb-3 useremail_div" style="display: none">
                        <input id="email" type="email" class="form-control  @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" autocomplete="email" autofocus
                            placeholder="Enter Email" onchange="trimInput(this)">
                    </div>

                    <div class="form-group mb-3 userphone_div" style="display: none">
                        <input id="phone" type="text" class="form-control  @error('phone') is-invalid @enderror"
                            name="phone" value="{{ old('phone') }}" autocomplete="phone" autofocus
                            placeholder="Enter phone">
                    </div>

                    <div class="form-group mb-3 user_div">
                        <input id="user_id" type="text" class="form-control @error('user_id') is-invalid @enderror"
                            name="user_id" value="{{ old('user_id') }}" placeholder="Enter User ID" autofocus
                            onchange="validateUserId()">
                        <div id="user_id_error" class="invalid-feedback" style="display: none;">
                            User ID format is not valid. It must start with 3 letters followed by numbers.
                        </div>
                    </div>

                    <div class="input-group mb-3 password_div" style="display: none">
                        <input id="password" type="password"
                            class="form-control @error('password') is-invalid @enderror" name="password"
                            autocomplete="current-password" placeholder="Password"  onchange="trimInput(this)">
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

                    <div class="input-group mb-3 otp_div" style="display: none">
                        <input id="otp" type="text" class="form-control @error('otp') is-invalid @enderror"
                            name="otp" autocomplete="current-otp" placeholder="otp" min="0">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-sm btn-primary" id="resend-otp">Resend</button>
                        </div>

                        @error('otp')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="row">

                        <div class="col-4">
                            <span>Number: </span><span id="captchaNumber"></span>
                        </div>
                        <div class="input-group col-12 mb-3">
                            <input type="number" placeholder="Enter above number" class="form-control"
                                id="captchaInput" name="captchaInput" required
                                oninput="this.value = this.value.slice(0, 4);">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-sm btn-primary"
                                    onclick="generateCaptcha()">Generate
                                    New</button>
                            </div>
                        </div>

                        <div class="col-12" id="verifyBtnDiv">
                            <button type="button" class="btn btn-primary btn-block" id="verifyBtn">Continue</button>
                        </div>
                        <!-- /.col -->
                        <div class="col-12" style="display: none" id="submitBtnDiv">
                            <button type="button" class="btn btn-primary btn-block" id="signBtn">Sign In</button>
                        </div>

                        <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <script src="{{ asset('/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('customdownload/js/toastr.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <script>
        function trimInput(input) {
            input.value = input.value.trim();
        }

        @if (session('status') || session('error'))

            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-center",
                "timeOut": "5000"
            }
            @if (session('status'))
                toastr.success("{!! Session::get('status') !!}");
            @else
                toastr.error("{!! Session::get('error') !!}");
            @endif
        @endif
        const csrfToken = "{{ csrf_token() }}";
        const validateRoute = "{{ route('member.memberexist') }}";
        const otpResendRoute = "{{ route('member.resendotp') }}";
        const loginRoute = "{{ route('member.loginvalidate') }}";
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

        function createMessage(message, type = "success") {
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-center",
                "timeOut": "5000"
            }
            if (type == "success") {
                toastr.success(message);
            } else if (type == "error") {
                toastr.error(message);
            }

        }

        function showLoader(processName) {
            $("#loader-text").text(processName);
            $("#loader").show();
        }

        function hideLoader() {
            $("#loader").hide();
        }
        $(document).ajaxStart(function() {
            // Show loader with a default process name if not specified
            showLoader("Processing...");
        }).ajaxStop(function() {
            hideLoader();
        });

        function validateUserId() {
            const userIdInput = document.getElementById('user_id');
            const errorDiv = document.getElementById('user_id_error');
            const userId = userIdInput.value;

            // Updated regular expression: 2 or more letters followed by numbers.
            const regex =
            /^[A-Za-z]{2,}_?\d+$/; // This allows for two or more letters followed by optional underscore and numbers

            if (!regex.test(userId)) {
                errorDiv.style.display = 'block'; // Show error
                userIdInput.classList.add('is-invalid');
            } else {
                errorDiv.style.display = 'none'; // Hide error
                userIdInput.classList.remove('is-invalid');
            }
        }
    </script>
    <script src="{{ asset('custom\member\js\login.js') }}"></script>

</body>

</html>
