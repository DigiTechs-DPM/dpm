<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-category">PPC Data</li>
        @if (isFinance())
            <li class="nav-item {{ request()->route()->getName() === 'admin.account-keys.get' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.account-keys.get') }}">
                    <span class="icon-bg"><i class="mdi mdi-currency-usd menu-icon"></i></span>
                    <span class="menu-title">Account Keys</span>
                </a>
            </li>
            <li class="nav-item {{ request()->route()->getName() === 'admin.brand-payments.get' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.brand-payments.get') }}">
                    <span class="icon-bg"><i class="mdi mdi-currency-usd menu-icon"></i></span>
                    <span class="menu-title">Brand Payments</span>
                </a>
            </li>
        @else
            <li class="nav-item {{ request()->route()->getName() === 'admin.index.get' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.index.get') }}">
                    <span class="icon-bg"><i class="mdi mdi-cube menu-icon"></i></span>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>
            <li class="nav-item {{ request()->route()->getName() === 'admin.account-keys.get' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.account-keys.get') }}">
                    <span class="icon-bg"><i class="mdi mdi-currency-usd menu-icon"></i></span>
                    <span class="menu-title">Account Keys</span>
                </a>
            </li>
            <li class="nav-item {{ request()->route()->getName() === 'admin.domain-script.get' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.domain-script.get') }}">
                    <span class="icon-bg"><i class="mdi mdi-script-outline menu-icon"></i></span>
                    <span class="menu-title">Script</span>
                </a>
            </li>
            <li class="nav-item {{ request()->route()->getName() === 'admin.clients.get' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.clients.get') }}">
                    <span class="icon-bg"><i class="mdi mdi-account-group menu-icon"></i></span>
                    <span class="menu-title">Clients</span>
                </a>
            </li>
            <li class="nav-item {{ request()->route()->getName() === 'admin.brands.get' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.brands.get') }}">
                    <span class="icon-bg"><i class="mdi mdi-web menu-icon"></i></span>
                    <span class="menu-title">Brands</span>
                </a>
            </li>
            <li class="nav-item {{ request()->route()->getName() === 'admin.sellers.get' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.sellers.get') }}">
                    <span class="icon-bg"><i class="mdi mdi-account menu-icon"></i></span>
                    <span class="menu-title">Sellers</span>
                </a>
            </li>
            @if (isProjectManager())
                <li
                    class="nav-item {{ request()->route()->getName() === 'admin.assigned-leads.get' ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.assigned-leads.get') }}">
                        <span class="icon-bg"><i class="mdi mdi-basket menu-icon"></i></span>
                        <span class="menu-title">Assigned Leads</span>
                    </a>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.leads.get') }}">
                        <span class="icon-bg"><i class="mdi mdi-cart-arrow-down menu-icon"></i></span>
                        <span class="menu-title">Leads</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.orders.get') }}">
                        <span class="icon-bg"><i class="mdi mdi-currency-usd menu-icon"></i></span>
                        <span class="menu-title">Orders</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->route()->getName() === 'admin.payments.get' ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.payments.get') }}">
                        <span class="icon-bg"><i class="mdi mdi-currency-usd menu-icon"></i></span>
                        <span class="menu-title">Payments</span>
                    </a>
                </li>
                <li class="nav-item nav-category">Upwork Data</li>
                <li class="nav-item {{ request()->route()->getName() === 'admin.upwork-clients.get' ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.upwork-clients.get') }}">
                        <span class="icon-bg"><i class="mdi mdi-account-group menu-icon"></i></span>
                        <span class="menu-title">Upwork Clients</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->route()->getName() === 'admin.upwork-orders.get' ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.upwork-orders.get') }}">
                        <span class="icon-bg"><i class="mdi mdi-currency-usd menu-icon"></i></span>
                        <span class="menu-title">Upwork Order</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->route()->getName() === 'admin.upwork-payments.get' ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.upwork-payments.get') }}">
                        <span class="icon-bg"><i class="mdi mdi-currency-usd menu-icon"></i></span>
                        <span class="menu-title">Upwork Payments</span>
                    </a>
                </li>
            @endif
        @endif
    </ul>
</nav>
