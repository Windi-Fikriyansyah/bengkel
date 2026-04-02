<ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
        <a href="{{route('dashboard')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div data-i18n="Analytics">Dashboard</div>
        </a>
    </li>

    <!-- Layanan -->
    <li class="menu-item {{ request()->is('layanan*') ? 'active' : '' }}">
        <a href="{{route('layanan.index')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-briefcase"></i>
            <div data-i18n="Layanan">Layanan</div>
        </a>
    </li>

    <!-- Sparepart (Next step) -->
    <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link text-muted">
            <i class="menu-icon tf-icons bx bx-package"></i>
            <div data-i18n="Sparepart">Sparepart (Segera)</div>
        </a>
    </li>



</ul>