<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('page_title'); ?></title>
    <?php echo $__env->yieldContent('header-script'); ?>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo e(asset('plugins/fontawesome-free/css/all.min.css')); ?>">
    <link href="<?php echo e(asset('customdownload/css/toastr.min.css')); ?>" rel="stylesheet">
    <!-- Ionicons -->
    
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="<?php echo e(asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')); ?>">
    <!-- iCheck -->
    
    <!-- JQVMap -->
    
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo e(asset('dist/css/adminlte.min.css')); ?>">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?php echo e(asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css')); ?>">
    <style>
        .error{
            color: red;
        }
    </style>
    <!-- Daterange picker -->
    
    <!-- summernote -->
    
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="<?php echo e(route('voting.index')); ?>" class="brand-link">
                <img src="<?php echo e(asset('homepage/assets/img/logo.png')); ?>" alt="AdminLTE Logo"
                    class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">India E-Voting</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="<?php echo e(asset('dist/img/avatar5.png')); ?>" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block"><?php echo e(auth()->user()->name); ?></a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <?php echo $__env->make('app.layout.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <nav class="navbar navbar-expand navbar-white ">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                            </li>
                        </ul>
                    </nav>
                    <?php echo $__env->yieldContent('content-header'); ?>
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <?php echo $__env->yieldContent('content-body'); ?>
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
           <strong>© 2024 India E-Voting. </strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <ul class="list-inline footer-links"> 
                        <li class="list-inline-item"> 
                            <a href="<?php echo e(route('policy')); ?>" class=""> 
                                Privacy Policy 
                            </a> 
                        </li> 
                        <li class="list-inline-item"> 
                            <a href="<?php echo e(route('policy')); ?>" class=""> 
                                Terms of Service 
                            </a> 
                        </li> 
                        
                    </ul> 
            </div>
        </footer>

    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="<?php echo e(asset('plugins/jquery/jquery.min.js')); ?>"></script>
    <!-- jQuery UI 1.11.4 -->
    
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <!-- Bootstrap 4 -->
    
    
    <!-- AdminLTE App -->
    <script src="<?php echo e(asset('dist/js/adminlte.js')); ?>"></script>
    <!-- AdminLTE for demo purposes -->
    
    <script src="<?php echo e(asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js')); ?>"></script>
    <script src="<?php echo e(asset('customdownload/js/sweetalert.min.js')); ?>"></script>
    <script src="<?php echo e(asset('customdownload/js/sweetalert2@11.js')); ?>"></script>
    <script src="<?php echo e(asset('customdownload/js/toastr.js')); ?>"></script>


    <script>
        $(document).ready(function() {
            $("input[data-bootstrap-switch]").each(function() {
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            })
        })
        <?php if(session('status')): ?>

            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-center",
                "timeOut": "5000"
            }
            toastr.success("<?php echo Session::get('status'); ?>");
        <?php endif; ?>
        <?php if(session('error')): ?>

            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-center",
                "timeOut": "5000"
            }
            toastr.error("<?php echo Session::get('error'); ?>");
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
<?php /**PATH C:\Dhaval\Web Development\LARAVEL\testproject2.0\resources\views/app/layout/app.blade.php ENDPATH**/ ?>