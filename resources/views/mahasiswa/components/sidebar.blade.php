<div class="sidebar">
    <div class="sidebar-header">
        <h5 class="sidebar-title">
            @if(request()->routeIs('mahasiswa.dashboard*'))
                Ringkasan Progress
            @elseif(request()->routeIs('mahasiswa.profile'))
                Profil
            @else
                Materi PBO
            @endif
        </h5>
    </div>

    @if(request()->routeIs('mahasiswa.profile') && auth()->check())
        {{-- Show only Dashboard and Profile menu when on profile page (except for guests) --}}
        <ul class="nav-menu">
            <li>
                <a href="{{ route('mahasiswa.dashboard') }}"
                   class="menu-item {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('mahasiswa.profile') }}" 
                   class="menu-item {{ request()->routeIs('mahasiswa.profile') ? 'active' : '' }}">
                    <i class="fas fa-user"></i>
                    <span>Profil Saya</span>
                </a>
            </li>
        </ul>
    @elseif(request()->routeIs('mahasiswa.dashboard*') && auth()->check())
        {{-- Dashboard Sidebar Menu (except for guests) --}}
        <ul class="nav-menu">
            <li>
                <a href="{{ route('mahasiswa.dashboard') }}" 
                   class="menu-item {{ request()->routeIs('mahasiswa.dashboard') && !request()->routeIs('mahasiswa.dashboard.*') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Beranda</span>
                </a>
            </li>
            <li>
                <a href="{{ route('mahasiswa.dashboard.in-progress') }}" 
                   class="menu-item {{ request()->routeIs('mahasiswa.dashboard.in-progress') ? 'active' : '' }}">
                    <i class="fas fa-spinner"></i>
                    <span>Sedang Dipelajari</span>
                </a>
            </li>
            <li>
                <a href="{{ route('mahasiswa.dashboard.completed') }}" 
                   class="menu-item {{ request()->routeIs('mahasiswa.dashboard.completed') ? 'active' : '' }}">
                    <i class="fas fa-check-circle"></i>
                    <span>Selesai</span>
                </a>
            </li>
        </ul>
    @else
        {{-- Materials Sidebar Menu --}}
        @isset($materials)
            <ul class="nav-menu">
                @foreach($materials as $m)
                    <li>
                        <a href="{{ route('mahasiswa.materials.show', $m->id) }}" 
                           class="menu-item {{ isset($material) && $material->id == $m->id ? 'active' : '' }}">
                            <i class="fas fa-book"></i>
                            <span>{{ $m->title }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endisset
    @endif

    @unless(request()->routeIs('mahasiswa.profile') || auth()->check())
        {{-- Profile Section Divider (hide for guests) --}}
        <div class="sidebar-header mt-4">
            <h5 class="sidebar-title">Profil</h5>
        </div>
        
        {{-- Profile Menu Items (hide for guests) --}}
        <ul class="nav-menu">
            <li>
                <a href="{{ route('mahasiswa.profile') }}" 
                   class="menu-item {{ request()->routeIs('mahasiswa.profile') ? 'active' : '' }}">
                    <i class="fas fa-user"></i>
                    <span>Profil Saya</span>
                </a>
            </li>
        </ul>
    @endunless
</div>