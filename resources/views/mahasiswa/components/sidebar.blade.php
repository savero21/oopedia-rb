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

    @if(request()->routeIs('mahasiswa.profile'))
        {{-- Show only Dashboard and Profile menu when on profile page --}}
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
    @elseif(request()->routeIs('mahasiswa.dashboard*'))
        {{-- Dashboard Sidebar Menu --}}
        <ul class="nav-menu">
            <li>
                <a href="{{ route('mahasiswa.dashboard') }}"
                   class="menu-item {{ request()->routeIs('mahasiswa.dashboard') && !request()->routeIs('*.in-progress') && !request()->routeIs('*.complete') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Semua Progress</span>
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
                <a href="{{ route('mahasiswa.dashboard.complete') }}"
                   class="menu-item {{ request()->routeIs('mahasiswa.dashboard.complete') ? 'active' : '' }}">
                    <i class="fas fa-check-circle"></i>
                    <span>Selesai</span>
                </a>
            </li>
        </ul>
    @else
        <ul class="nav-menu">
            @foreach($materials as $material)
                <li>
                    <a href="{{ route('mahasiswa.materials.show', $material) }}"
                       class="menu-item {{ request()->is('mahasiswa/materials/' . $material->id) ? 'active' : '' }}">
                        <i class="fas fa-book"></i>
                        <span>{{ $material->title }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    @unless(request()->routeIs('mahasiswa.profile'))
        {{-- Profile Section Divider --}}
        <div class="sidebar-header mt-4">
            <h5 class="sidebar-title">Profil</h5>
        </div>
        
        {{-- Profile Menu Items --}}
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