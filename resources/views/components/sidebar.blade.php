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

        <!-- Master Data -->
        <li>
            <a href="javascript:void(0);"
                class="sidebar-menu {{ request()->routeIs([
                    'banks.index', 
                    'banks.create', 
                    'banks.edit', 
                    'banks.show',
                    'customer-groups.index', 
                    'customer-groups.create', 
                    'customer-groups.edit', 
                    'customer-groups.show',
                    'category-customers.index', 
                    'category-customers.create', 
                    'category-customers.edit', 
                    'category-customers.show',
                    'marketing-groups.index', 
                    'marketing-groups.create', 
                    'marketing-groups.edit', 
                    'marketing-groups.show',
                    'mitra-groups.index', 
                    'mitra-groups.create', 
                    'mitra-groups.edit', 
                    'mitra-groups.show',
                ]) ? 'active' : '' }}">
                <span class="sidebar-menu-icon">
                    <i data-feather="database"></i>
                </span>
                <span class="sidebar-menu-text">Master Data</span>
                <span class="sidebar-menu-arrow">
                    <i data-feather="chevron-right"></i>
                </span>
            </a>
            <ul class="sidebar-submenu">
                @can('banks.view')
                    <li>
                        <a href="{{ route('banks.index') }}"
                            class="sidebar-submenu-item {{ request()->routeIs('banks.index', 'banks.create', 'banks.edit', 'banks.show') ? 'active' : '' }}">
                             Bank</a>
                    </li>
                @endcan
                @can('customer-groups.view')
                    <li>
                        <a href="{{ route('customer-groups.index') }}"
                            class="sidebar-submenu-item {{ request()->routeIs('customer-groups.index', 'customer-groups.create', 'customer-groups.edit', 'customer-groups.show') ? 'active' : '' }}">
                            Customer Group </a>
                    </li>
                @endcan
                @can('category-customers.view')
                    <li>
                        <a href="{{ route('category-customers.index') }}"
                            class="sidebar-submenu-item {{ request()->routeIs('category-customers.index', 'category-customers.create', 'category-customers.edit', 'category-customers.show') ? 'active' : '' }}">
                            Category Customer </a>
                    </li>
                @endcan
                @can('marketing-groups.view')
                    <li>
                        <a href="{{ route('marketing-groups.index') }}"
                            class="sidebar-submenu-item {{ request()->routeIs('marketing-groups.index', 'marketing-groups.create', 'marketing-groups.edit', 'marketing-groups.show') ? 'active' : '' }}">
                            Marketing Group </a>
                    </li>
                @endcan
                @can('mitra-groups.view')
                    <li>
                        <a href="{{ route('mitra-groups.index') }}"
                            class="sidebar-submenu-item {{ request()->routeIs('mitra-groups.index', 'mitra-groups.create', 'mitra-groups.edit', 'mitra-groups.show') ? 'active' : '' }}">
                            Mitra Group </a>
                    </li>
                @endcan
                {{-- catgory product --}}
                @can('category_product.view')
                    <li>
                        <a href="{{ route('category-products.index') }}"
                            class="sidebar-submenu-item {{ request()->routeIs('category-products.index', 'category-products.create', 'category-products.edit', 'category-products.show') ? 'active' : '' }}">
                            Category Product </a>
                    </li>
                @endcan
                <li>
                    {{-- taxes --}}
                    <a href="{{ route('taxes.index') }}"
                        class="sidebar-submenu-item {{ request()->routeIs(['taxes.*']) ? 'active' : '' }}"
                        >
                            Taxes                       
                    </a>
                </li>
            </ul>
        </li>
        <!-- Shipping -->
<li>
    <a href="javascript:void(0);"
        class="sidebar-menu {{ request()->routeIs(['shippings.*']) ? 'active' : '' }}">
        <span class="sidebar-menu-icon">
            <i data-feather="truck"></i>
        </span>
        <span class="sidebar-menu-text">Shipping</span>
        <span class="sidebar-menu-arrow">
            <i data-feather="chevron-right"></i>
        </span>
    </a>
    <ul class="sidebar-submenu">
        @can('shipping.view')
            <li>
                <a href="{{ route('shippings.index') }}"
                    class="sidebar-submenu-item {{ request()->routeIs('shippings.index') ? 'active' : '' }}">
                    Shipping List</a>
            </li>
        @endcan
        @can('shipping.create')
            <li>
                <a href="{{ route('shippings.create') }}"
                    class="sidebar-submenu-item {{ request()->routeIs('shippings.create') ? 'active' : '' }}">
                    Create Shipping</a>
            </li>
        @endcan
    </ul>
</li>
        <!-- Customer -->
        <li>
            <a href="javascript:void(0);"
                class="sidebar-menu {{ request()->routeIs(['customers.*']) ? 'active' : '' }}">
                <span class="sidebar-menu-icon">
                    <i data-feather="users"></i>
                </span>
                <span class="sidebar-menu-text">Customers</span>
                <span class="sidebar-menu-arrow">
                    <i data-feather="chevron-right"></i>
                </span>
            </a>
            <ul class="sidebar-submenu">
                @can('customer.view')
                    <li>
                        <a href="{{ route('customers.index') }}"
                            class="sidebar-submenu-item {{ request()->routeIs('customers.index') ? 'active' : '' }}">
                            Customer List</a>
                    </li>
                @endcan
                @can('customer.create')
                    <li>
                        <a href="{{ route('customers.create') }}"
                            class="sidebar-submenu-item {{ request()->routeIs('customers.create') ? 'active' : '' }}">
                            Create Customer</a>
                    </li>
                @endcan
            </ul>
        </li>
        
        <!-- Marketing -->
        <li>
            <a href="javascript:void(0);"
                class="sidebar-menu {{ request()->routeIs(['marketings.*']) ? 'active' : '' }}">
                <span class="sidebar-menu-icon">
                    <i data-feather="dollar-sign"></i>
                </span>
                <span class="sidebar-menu-text">Marketing</span>
                <span class="sidebar-menu-arrow">
                    <i data-feather="chevron-right"></i>
                </span>
            </a>
            <ul class="sidebar-submenu">
                @can('marketing.view')
                    <li>
                        <a href="{{ route('marketings.index') }}"
                            class="sidebar-submenu-item {{ request()->routeIs('marketings.index') ? 'active' : '' }}">
                            Marketing</a>
                    </li>
                @endcan
                @can('marketing.create')
                    <li>
                        <a href="{{ route('marketings.create') }}"
                            class="sidebar-submenu-item {{ request()->routeIs('marketings.create') ? 'active' : '' }}">
                            Create Marketing</a>
                    </li>
                @endcan
            </ul>
        </li>
        <!-- Mitra -->
        <li>
            <a href="javascript:void(0);"
                class="sidebar-menu {{ request()->routeIs(['mitras.*']) ? 'active' : '' }}">
                <span class="sidebar-menu-icon">
                    <i data-feather="users"></i>
                </span>
                <span class="sidebar-menu-text">Mitra</span>
                <span class="sidebar-menu-arrow">
                    <i data-feather="chevron-right"></i>
                </span>
            </a>
            <ul class="sidebar-submenu">
                @can('mitra.view')
                    <li>
                        <a href="{{ route('mitras.index') }}"
                            class="sidebar-submenu-item {{ request()->routeIs('mitras.index') ? 'active' : '' }}">
                            Mitra</a>
                    </li>
                @endcan
                @can('mitra.create')
                    <li>
                        <a href="{{ route('mitras.create') }}"
                            class="sidebar-submenu-item {{ request()->routeIs('mitras.create') ? 'active' : '' }}">
                            Create Mitra</a>
                    </li>
                @endcan
            </ul>
        </li>
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