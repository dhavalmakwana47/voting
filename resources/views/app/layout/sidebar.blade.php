<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Add icons to the links using the .nav-icon class -->
    <li class="nav-item">
      <a href="{{ route('home') }}" class="nav-link {{ Request::routeIs('home') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Dashboard</p>
      </a>
    </li>
    @if (permissionCheck())
    <li class="nav-item">
      <a href="{{ route('users.index') }}" class="nav-link {{ Request::routeIs('users.index') ? 'active' : '' }}">
        <i class="nav-icon fas fa-users"></i>
        <p>Users</p>
      </a>
    </li>
    @endif
    @if (permissionCheck())
    <li class="nav-item">
      <a href="{{ route('company.index') }}" class="nav-link {{ Request::routeIs('company.index') ? 'active' : '' }}">
        <i class="nav-icon fas fa-building"></i>
        <p>Company</p>
      </a>
    </li>
    @endif
    @if (permissionCheck())
    <li class="nav-item">
      <a href="{{ route('usercompanymap.create') }}" class="nav-link {{ Request::routeIs('usercompanymap.create') ? 'active' : '' }}">
        <i class="nav-icon fas fa-user-tag"></i>
        <p>Assign</p>
      </a>
    </li>
    @endif

    <li class="nav-item">
      <a href="{{ route('voting.index') }}" class="nav-link {{ Request::routeIs('voting.index','member.index') ? 'active' : '' }}">
        <i class="nav-icon fas fa-vote-yea"></i>
        <p>Voting Information</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ route('vote.index') }}" class="nav-link {{ Request::routeIs('vote.index') ? 'active' : '' }}">
        <i class="nav-icon fas fa-check-circle"></i>
        <p>Status of Voting</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ route('votingreport.index') }}" class="nav-link {{ Request::routeIs('votingreport.index') ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-alt"></i>
        <p>Finalize Voting Report</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ route('userlog.index') }}" class="nav-link {{ Request::routeIs('userlog.index') ? 'active' : '' }}">
        <i class="nav-icon fas fa-history"></i>
        <p>User Log</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ route('userpassword.change') }}" class="nav-link {{ Request::routeIs('userpassword.change') ? 'active' : '' }}">
        <i class="nav-icon fas fa-key"></i>
        <p>Change Password</p>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="{{ route('logout') }}"
         onclick="event.preventDefault();
                   document.getElementById('logout-form').submit();">
        <i class="nav-icon fas fa-sign-out-alt"></i>
        <p>Logout</p>
      </a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
      </form>
    </li>
  </ul>
</nav>
