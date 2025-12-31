<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'indiaevoting')); ?></title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo e(asset('plugins/fontawesome-free/css/all.min.css')); ?>">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?php echo e(asset('/plugins/icheck-bootstrap/icheck-bootstrap.min.css')); ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo e(asset('/dist/css/adminlte.min.css')); ?>">
    <link href="<?php echo e(asset('customdownload/css/toastr.min.css')); ?>" rel="stylesheet">
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
    </style>
      

</head>

<body <?php echo $__env->yieldContent('body-attribute '); ?>>
    <?php echo $__env->yieldContent('content'); ?>
    <script src="<?php echo e(asset('/plugins/jquery/jquery.min.js')); ?>"></script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo e(asset('plugins/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo e(asset('dist/js/adminlte.min.js')); ?>"></script>
    <script src="<?php echo e(asset('customdownload/js/toastr.js')); ?>"></script>
    <script>
        <?php if(session('status')): ?>

            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-center",
                "timeOut": "5000"
            }
            toastr.success("<?php echo Session::get('status'); ?>");
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
    </script>
    <?php echo $__env->yieldContent('footer-script'); ?>
</body>

</html>
<?php /**PATH C:\Dhaval\Web Development\Git\voting\resources\views/layouts/app.blade.php ENDPATH**/ ?>