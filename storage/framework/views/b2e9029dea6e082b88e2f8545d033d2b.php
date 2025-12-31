<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('page_title'); ?></title>
    <?php echo $__env->yieldContent('header-script'); ?>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo e(asset('plugins/fontawesome-free/css/all.min.css')); ?>">
    <link href="<?php echo e(asset('customdownload/css/toastr.min.css')); ?>" rel="stylesheet">
    <link rel="stylesheet"
        href="<?php echo e(asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('dist/css/adminlte.min.css')); ?>">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?php echo e(asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css')); ?>">
    <link href="<?php echo e(asset('customdownload/css/toastr.min.css')); ?>" rel="stylesheet">
    <?php echo $__env->yieldContent('header-script'); ?>
    <style>
        #loader {
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            text-align: center;
        }

        #loader img {
            display: block;
            margin: 0 auto;
        }

        #loader-text {
            margin-top: 10px;
            font-size: 16px;
            color: #000;
        }
    </style>
</head>

<body>
    <div id="loader" style="display:none;">
        <img src="<?php echo e(asset('loading-4802_256.gif')); ?>" alt="Loading...">
        <p id="loader-text"></p>
    </div>
    <!-- ./wrapper -->
    <div class="wrapper">
        <nav class="navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <a class="navbar-brand d-flex" href="<?php echo e(route('member.voting_list')); ?>">
                <img src="<?php echo e(asset('homepage/assets/img/logo.png')); ?>" alt="logo" style="width:100px;" class="mr-2"> 
            </a>
            <ul class="navbar-nav">
                
                <li class="nav-item d-sm-inline-block">
                    <a href="<?php echo e(route('member.voting_list')); ?>" class="nav-link">Home</a>
                </li>
                <?php if(session('login_type')  == 'user_name'): ?>
                    
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="<?php echo e(route('member.change_password')); ?>" class="nav-link">Change Password</a>
                </li>
                <?php endif; ?>

                <li class="nav-item d-sm-inline-block">
                    <a href="<?php echo e(route('member.logout')); ?>" class="nav-link">Logout</a>
                </li>
            </ul>
        </nav>


        <!-- Main content -->
        <?php echo $__env->yieldContent('body-content'); ?>
        <!-- /.content -->

        <footer class="text-center bg-body-tertiary">
            <!-- Copyright -->
            <!-- <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.05);">
            © 2024 Copyright:
            <a class="text-body" href="<?php echo e(route('index')); ?>">India E-Voting </a>
          </div> -->
            <!-- Copyright -->
            <div class="row p-3" style="background-color: rgba(0, 0, 0, 0.05);">
                <div class="col-md-6">
                    <p>© 2024 India E-Voting. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <ul class="list-inline footer-links">
                        <li class="list-inline-item">
                            <a href="<?php echo e(route('policy')); ?>" class="">
                                Privacy Policy
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#" class="">
                                Terms of Service
                            </a>
                        </li>
                        
                    </ul>
                </div>
            </div>
        </footer>

    </div>
    <!-- jQuery -->
    <script src="<?php echo e(asset('plugins/jquery/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('dist/js/adminlte.js')); ?>"></script>
    <script src="<?php echo e(asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js')); ?>"></script>
    <script src="<?php echo e(asset('customdownload/js/sweetalert.min.js')); ?>"></script>
    <script src="<?php echo e(asset('customdownload/js/sweetalert2@11.js')); ?>"></script>
    <script src="<?php echo e(asset('customdownload/js/toastr.js')); ?>"></script>
    <script src="<?php echo e(asset('plugins/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>

    <script>
        <?php if(session('status') || session('error')): ?>

            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-center",
                "timeOut": "5000"
            }
            <?php if(session('status')): ?>
                toastr.success("<?php echo Session::get('status'); ?>");
            <?php else: ?>
                toastr.error("<?php echo Session::get('error'); ?>");
            <?php endif; ?>
        <?php endif; ?>

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
    </script>
    <?php echo $__env->yieldContent('footer-script'); ?>
</body>

</html>
<?php /**PATH C:\Dhaval\Web Development\LARAVEL\testproject2.0\resources\views/app/member/layout.blade.php ENDPATH**/ ?>