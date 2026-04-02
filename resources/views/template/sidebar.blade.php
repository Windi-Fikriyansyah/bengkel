<ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
        <a href="{{route('dashboard')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div data-i18n="Analytics">Dashboard</div>
        </a>
    </li>

    <!-- Transaksi -->
    <li class="menu-item {{ request()->is('transaksi*') ? 'active' : '' }}">
        <a href="{{route('transaksi.index')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-cart"></i>
            <div data-i18n="Transaksi">Transaksi / Nota</div>
        </a>
    </li>

    <!-- Pelanggan -->
    <li class="menu-item {{ request()->is('pelanggan*') ? 'active' : '' }}">
        <a href="{{route('pelanggan.index')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-user"></i>
            <div data-i18n="Pelanggan">Data Pelanggan</div>
        </a>
    </li>

    <!-- Layanan -->
    <li class="menu-item {{ request()->is('layanan*') ? 'active' : '' }}">
        <a href="{{route('layanan.index')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-briefcase"></i>
            <div data-i18n="Layanan">Layanan</div>
        </a>
    </li>

    <!-- Sparepart -->
    <li class="menu-item {{ request()->is('sparepart*') ? 'active' : '' }}">
        <a href="{{route('sparepart.index')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-package"></i>
            <div data-i18n="Sparepart">Sparepart</div>
        </a>
    </li>

    <!-- Stok Masuk -->
    <li class="menu-item {{ request()->is('stok-masuk*') ? 'active' : '' }}">
        <a href="{{route('stok-masuk.index')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-archive-in"></i>
            <div data-i18n="Stok Masuk">Stok Masuk</div>
        </a>
    </li>



</ul>