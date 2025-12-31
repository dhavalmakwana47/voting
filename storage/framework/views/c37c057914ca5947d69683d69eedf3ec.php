
<?php $__env->startSection('body-attribute '); ?>
    class="hold-transition login-page"
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="login-box">
        <div class="login-logo">
            <img src="<?php echo e(asset('homepage/assets/img/logo.png')); ?>" alt="logo" style="width:100px;" class="mr-2"> 

            <span><b>Admin </b>Login</span>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Log in to begin your session</p>

                <form method="POST" action="<?php echo e(route('login')); ?>" id="login-form">
                    <?php echo csrf_field(); ?>

                    <div class="input-group mb-3">
                        <input id="email" type="email" class="form-control  <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            name="email" value="<?php echo e(old('email')); ?>" autocomplete="email" autofocus placeholder="Email" onchange="trimInput(this)">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="invalid-feedback" role="alert">
                                <strong><?php echo e($message); ?></strong>
                            </span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="input-group mb-3">
                        <input id="password" type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            name="password" autocomplete="current-password" placeholder="Password"  onchange="trimInput(this)">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>

                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="invalid-feedback" role="alert">
                                <strong><?php echo e($message); ?></strong>
                            </span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                        
                        <!-- /.col -->
                        <div class="col-4 mt-3">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
                <!-- /.social-auth-links -->
                <?php if(Route::has('password.request')): ?>
                    <p class="mb-1">
                        <a href="<?php echo e(route('password.request')); ?>"> <?php echo e(__('Forgot Your Password?')); ?></a>
                    </p>
                <?php endif; ?>

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

<?php $__env->stopSection(); ?>
<?php $__env->startSection('footer-script'); ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Dhaval\Web Development\Git\voting\resources\views/auth/login.blade.php ENDPATH**/ ?>