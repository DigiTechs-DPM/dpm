<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-category">Main</li>
        @if (isProjectManager())
            <li class="nav-item {{ request()->route()->getName() === 'seller.clients.get' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('seller.clients.get') }}">
                    <span class="icon-bg"><i class="mdi mdi-account-group menu-icon"></i></span>
                    <span class="menu-title">Clients</span>
                </a>
            </li>
            <li class="nav-item {{ request()->route()->getName() === 'seller.brands.get' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('seller.brands.get') }}">
                    <span class="icon-bg"><i class="mdi mdi-web menu-icon"></i></span>
                    <span class="menu-title">Brands</span>
                </a>
            </li>
            <li class="nav-item {{ request()->route()->getName() === 'seller.sellers.get' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('seller.sellers.get') }}">
                    <span class="icon-bg"><i class="mdi mdi-account menu-icon"></i></span>
                    <span class="menu-title">Sellers</span>
                </a>
            </li>
            <li class="nav-item {{ request()->route()->getName() === 'seller.assigned-leads.get' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('seller.assigned-leads.get') }}">
                    <span class="icon-bg"><i class="mdi mdi-basket menu-icon"></i></span>
                    <span class="menu-title">Assigned Leads</span>
                </a>
            </li>
            <li
                class="nav-item {{ request()->route()->getName() === 'seller.assigned-leads-orders.get' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('seller.assigned-leads-orders.get') }}">
                    <span class="icon-bg"><i class="mdi mdi-currency-usd menu-icon"></i></span>
                    <span class="menu-title">Orders</span>
                </a>
            </li>
        @else
            <li class="nav-item {{ request()->route()->getName() === 'seller.index.get' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('seller.index.get') }}">
                    <span class="icon-bg"><i class="mdi mdi-cube menu-icon"></i></span>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>
            <li class="nav-item {{ request()->route()->getName() === 'seller.clients.get' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('seller.clients.get') }}">
                    <span class="icon-bg"><i class="mdi mdi-account-group menu-icon"></i></span>
                    <span class="menu-title">Clients</span>
                </a>
            </li>
            <li class="nav-item {{ request()->route()->getName() === 'seller.brands.get' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('seller.brands.get') }}">
                    <span class="icon-bg"><i class="mdi mdi-web menu-icon"></i></span>
                    <span class="menu-title">Brands</span>
                </a>
            </li>
            <li class="nav-item {{ request()->route()->getName() === 'seller.sellers.get' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('seller.sellers.get') }}">
                    <span class="icon-bg"><i class="mdi mdi-account menu-icon"></i></span>
                    <span class="menu-title">Sellers</span>
                </a>
            </li>
            <li class="nav-item {{ request()->route()->getName() === 'seller.leads.get' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('seller.leads.get') }}">
                    <span class="icon-bg"><i class="mdi mdi-cart-arrow-down menu-icon"></i></span>
                    <span class="menu-title">Leads</span>
                </a>
            </li>
            <li class="nav-item {{ request()->route()->getName() === 'seller.orders.get' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('seller.orders.get') }}">
                    <span class="icon-bg"><i class="mdi mdi-currency-usd menu-icon"></i></span>
                    <span class="menu-title">Orders</span>
                </a>
            </li>
            <li class="nav-item {{ request()->route()->getName() === 'seller.payments.get' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('seller.payments.get') }}">
                    <span class="icon-bg"><i class="mdi mdi-currency-usd menu-icon"></i></span>
                    <span class="menu-title">Payments</span>
                </a>
            </li>
        @endif
    </ul>
</nav>
