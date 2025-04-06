<aside class="sidebar">
    <!-- Sidebar Header Starts -->
    <a href="{{ route('admin.dashboard') }}">
        <div class="sidebar-header">
            <div class="sidebar-logo-icon">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </div>

            <div class="sidebar-logo-text">
                <h1 class="flex text-xl">
                    <span class="font-bold text-slate-800 dark:text-slate-200">
                        {{ hasSettings('site_name') ? getSettings('site_name') : config('app.name') }}
                    </span>
                </h1>

                <p class="whitespace-nowrap text-xs text-slate-400">
                    {{ hasSettings('site_title') ? getSettings('site_title') : config('app.name') }}
                </p>
            </div>
        </div>
    </a>
    <!-- Sidebar Header Ends -->

    <!-- Sidebar Menu Starts -->
    <ul class="sidebar-content">
        <!-- Dashboard -->
        @can('dashboard.view')
            <li>
                <a href="javascript:void(0);"
                    class="sidebar-menu  {{ request()->routeIs('admin.dashboard', 'admin.ecommerce.report') ? 'active' : '' }}">
                    <span class="sidebar-menu-icon">
                        <i data-feather="home"></i>
                    </span>
                    <span class="sidebar-menu-text">Dashboard</span>
                    <span class="sidebar-menu-arrow">
                        <i data-feather="chevron-right"></i>
                    </span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('admin.dashboard') }}"
                            class="sidebar-submenu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Analytics</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.ecommerce.report') }}"
                            class="sidebar-submenu-item {{ request()->routeIs('admin.ecommerce.report') ? 'active' : '' }}">Ecommerce</a>
                    </li>
                </ul>
            </li>
        @endcan

        <div class="sidebar-menu-header">Main Menu</div>

   
        <!-- Notification -->
  
      
        <!-- ecommnerce -->
        @can('ecommerce.view')
            <li>
                <a href="javascript:void(0);"
                    class="sidebar-menu {{ request()->routeIs([
                        'admin.product.index',
                        'admin.product.edit',
                        'admin.order.index',
                        'admin.order.show',
                        'admin.customer.index',
                    ])
                        ? 'active'
                        : '' }}">
                    <span class="sidebar-menu-icon">
                        <i data-feather="shopping-bag"></i>
                    </span>
                    <span class="sidebar-menu-text">Ecommerce</span>
                    <span class="sidebar-menu-arrow">
                        <i data-feather="chevron-right"></i>
                    </span>
                </a>
                <ul class="sidebar-submenu">
                    @can('product.view')
                        <li>
                            <a href="{{ route('admin.product.index') }}"
                                class="sidebar-submenu-item {{ request()->routeIs('admin.product.index') ? 'active' : '' }}">
                                Product List </a>
                        </li>
                    @endcan
                    @can('product.edit')
                        <li>
                            <a href="{{ route('admin.product.edit') }}"
                                class="sidebar-submenu-item {{ request()->routeIs('admin.product.edit') ? 'active' : '' }}">
                                Product Edit </a>
                        </li>
                    @endcan
                    @can('order.view')
                        <li>
                            <a href="{{ route('admin.order.index') }}"
                                class="sidebar-submenu-item {{ request()->routeIs('admin.order.index') ? 'active' : '' }}">
                                Order List </a>
                        </li>
                    @endcan
                    @can('order.view')
                        <li>
                            <a href="{{ route('admin.order.show') }}"
                                class="sidebar-submenu-item {{ request()->routeIs('admin.order.show') ? 'active' : '' }}">
                                Order Details </a>
                        </li>
                    @endcan
                    @can('customer.view')
                        <li>
                            <a href="{{ route('admin.customer.index') }}"
                                class="sidebar-submenu-item {{ request()->routeIs('admin.customer.index') ? 'active' : '' }}">
                                Customer List </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        <!-- Users -->
       
        <!--  Commponents  -->
        <div class="sidebar-menu-header">Pages</div>
    

            {{-- setting no ul--}}
        <li>
            <a 
                href="{{ route('admin.settings.index') }}"
                class="sidebar-menu {{ request()->routeIs([
                    'admin.settings.index',
                    'admin.settings.create',
                    'admin.settings.edit',
                ]) ? 'active' : '' }}">
                <span class="sidebar-menu-icon">
                    <i data-feather="settings"></i>
                </span>
                <span class="sidebar-menu-text">Settings</span>
            </a>
        </li>
        @can('notification.view')
        <li>
            <a href="{{ route('admin.notification.index') }}"
                class="sidebar-menu {{ request()->routeIs('admin.notification.index') ? 'active' : '' }}">
                <span class="sidebar-menu-icon">
                    <i data-feather="bell"></i>
                </span>
                <span class="sidebar-menu-text flex gap-2 items-center">
                    Notification

                    @if (auth()->user()->unreadNotifications->count() > 0)
                        <span
                            class="flex h-5 w-5 items-center justify-center rounded-full bg-danger-500 text-[11px] text-slate-200">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </span>

            </a>
        </li>
    @endcan

        @can('user.view')
        <li>
            <a href="javascript:void(0);"
                class="sidebar-menu {{ request()->routeIs(['admin.user.index', 'admin.user.create', 'admin.user.show', 'admin.user.edit', 'admin.roles.index', 'admin.roles.create', 'admin.roles.show', 'admin.roles.edit']) ? 'active' : '' }}">

                <span class="sidebar-menu-icon">
                    <i data-feather="users"></i>
                </span>
                <span class="sidebar-menu-text">Users</span>
                <span class="sidebar-menu-arrow">
                    <i data-feather="chevron-right"></i>
                </span>
            </a>
            <ul class="sidebar-submenu ">
                @can('user.view')
                    <li>
                        <a href="{{ route('admin.user.index') }}"
                            class="sidebar-submenu-item {{ request()->routeIs('admin.user.index') ? 'active' : '' }}">
                            Users</a>
                    </li>
                @endcan
                @can('role.view')
                    <li>
                        <a href="{{ route('admin.roles.index') }}"
                            class="sidebar-submenu-item {{ request()->routeIs('admin.roles.index') ? 'active' : '' }}">
                            Role & Permission </a>
                    </li>
                @endcan
            </ul>
        </li>
    @endcan
     
     
    </ul>
    <!-- Sidebar Menu Ends -->
</aside>
