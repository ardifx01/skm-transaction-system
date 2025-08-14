<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ url('/') }}" class="brand-link">
        <img src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="Logo" class="brand-image img-circle elevation-3"
            style="opacity:.8">
        <span class="brand-text font-weight-light">SKM System</span>
    </a>

    <div class="sidebar">
        @auth
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block">{{ Auth::user()->name }}</a>
                </div>
            </div>
        @endauth

        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- System Management -->
                @canany(['view-users', 'view-roles'])
                    <li class="nav-header">DATA MANAGEMENT</li>
                    <li
                        class="nav-item {{ Request::routeIs('users.*') || Request::routeIs('roles.*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ Request::routeIs('users.*') || Request::routeIs('roles.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>
                                System Management
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('view-users')
                                <li class="nav-item">
                                    <a href="{{ route('users.index') }}"
                                        class="nav-link {{ Request::routeIs('users.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Manage Users</p>
                                    </a>
                                </li>
                            @endcan
                            @can('view-roles')
                                <li class="nav-item">
                                    <a href="{{ route('roles.index') }}"
                                        class="nav-link {{ Request::routeIs('roles.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Roles & Permissions</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <!-- Policy Management -->
                @canany(['create-polis', 'view-polis'])
                    <li class="nav-header">POLICY MANAGEMENT</li>
                    @can('create-polis')
                        <li class="nav-item">
                            <a href="{{ route('policies.create') }}"
                                class="nav-link {{ Request::routeIs('policies.create') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-plus"></i>
                                <p>Create New Policy</p>
                            </a>
                        </li>
                    @endcan
                    @can('view-polis')
                        <li class="nav-item">
                            <a href="{{ route('policies.index') }}"
                                class="nav-link {{ Request::routeIs('policies.index') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-file-invoice"></i>
                                <p>Policy List</p>
                            </a>
                        </li>
                    @endcan
                @endcanany

                <!-- Reports -->
                @can('view-reports')
                    <li class="nav-header">REPORTS</li>
                    <li class="nav-item">
                        <a href="#" class="nav-link {{ Request::routeIs('reports.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Reports</p>
                        </a>
                    </li>
                @endcan

                <!-- Logout -->
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" class="nav-link"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </a>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>



<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">@yield('title')</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">@yield('title')</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
