<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Add icons to the links using the .nav-icon class -->
    <li class="nav-item">
      <a href="<?php echo e(route('home')); ?>" class="nav-link <?php echo e(Request::routeIs('home') ? 'active' : ''); ?>">
        <i class="nav-icon fas fa-home"></i>
        <p>Dashboard</p>
      </a>
    </li>
    <?php if(permissionCheck()): ?>
    <li class="nav-item">
      <a href="<?php echo e(route('users.index')); ?>" class="nav-link <?php echo e(Request::routeIs('users.index') ? 'active' : ''); ?>">
        <i class="nav-icon fas fa-users"></i>
        <p>Users</p>
      </a>
    </li>
    <?php endif; ?>
    <?php if(permissionCheck()): ?>
    <li class="nav-item">
      <a href="<?php echo e(route('company.index')); ?>" class="nav-link <?php echo e(Request::routeIs('company.index') ? 'active' : ''); ?>">
        <i class="nav-icon fas fa-building"></i>
        <p>Company</p>
      </a>
    </li>
    <?php endif; ?>
    <?php if(permissionCheck()): ?>
    <li class="nav-item">
      <a href="<?php echo e(route('usercompanymap.create')); ?>" class="nav-link <?php echo e(Request::routeIs('usercompanymap.create') ? 'active' : ''); ?>">
        <i class="nav-icon fas fa-user-tag"></i>
        <p>Assign</p>
      </a>
    </li>
    <?php endif; ?>

    <li class="nav-item">
      <a href="<?php echo e(route('voting.index')); ?>" class="nav-link <?php echo e(Request::routeIs('voting.index','member.index') ? 'active' : ''); ?>">
        <i class="nav-icon fas fa-vote-yea"></i>
        <p>Voting Information</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="<?php echo e(route('vote.index')); ?>" class="nav-link <?php echo e(Request::routeIs('vote.index') ? 'active' : ''); ?>">
        <i class="nav-icon fas fa-check-circle"></i>
        <p>Status of Voting</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="<?php echo e(route('votingreport.index')); ?>" class="nav-link <?php echo e(Request::routeIs('votingreport.index') ? 'active' : ''); ?>">
        <i class="nav-icon fas fa-file-alt"></i>
        <p>Finalize Voting Report</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="<?php echo e(route('userlog.index')); ?>" class="nav-link <?php echo e(Request::routeIs('userlog.index') ? 'active' : ''); ?>">
        <i class="nav-icon fas fa-history"></i>
        <p>User Log</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="<?php echo e(route('userpassword.change')); ?>" class="nav-link <?php echo e(Request::routeIs('userpassword.change') ? 'active' : ''); ?>">
        <i class="nav-icon fas fa-key"></i>
        <p>Change Password</p>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?php echo e(route('logout')); ?>"
         onclick="event.preventDefault();
                   document.getElementById('logout-form').submit();">
        <i class="nav-icon fas fa-sign-out-alt"></i>
        <p>Logout</p>
      </a>
      <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
        <?php echo csrf_field(); ?>
      </form>
    </li>
  </ul>
</nav>
<?php /**PATH C:\Dhaval\Web Development\Git\voting\resources\views/app/layout/sidebar.blade.php ENDPATH**/ ?>