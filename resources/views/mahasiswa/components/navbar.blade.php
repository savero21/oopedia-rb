@push('css')
<link href="{{ asset('css/mahasiswa.css') }}" rel="stylesheet">
@endpush

<nav class="navbar">
    <div class="container-fluid">
        <!-- Left side group -->
        <div class="d-flex align-items-center h-100">
            <!-- Logo -->
            <a class="navbar-brand me-4" href="{{ route('mahasiswa.dashboard') }}">
                <img src="{{ asset('images/logo.png') }}" alt="OOPEDIA" height="38">
            </a>
            
            <!-- Navigation links -->
            <div class="nav-links">
                <ul class="nav-menu">
                    @if(auth()->user()->role_id === 4)
                        <li>
                            <a href="{{ route('mahasiswa.materials.index') }}" 
                               class="nav-link {{ request()->routeIs('mahasiswa.materials*') ? 'active' : '' }}">
                                <i class="fas fa-book me-2"></i>
                                <span>Materi</span>
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('mahasiswa.dashboard') }}" 
                               class="nav-link {{ request()->routeIs('mahasiswa.dashboard*') ? 'active' : '' }}">
                                <i class="fas fa-chart-line me-2"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('mahasiswa.materials.index') }}" 
                               class="nav-link {{ request()->routeIs('mahasiswa.materials*') ? 'active' : '' }}">
                                <i class="fas fa-book me-2"></i>
                                <span>Materi</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Right side - Profile/Logout -->
        <div class="d-flex align-items-center">
            <div class="dropdown">
                <a class="nav-link dropdown-toggle profile-dropdown" href="#" role="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ asset('images/profile.gif') }}" alt="Profile" class="profile-image me-2">
                    <span class="me-2">{{ auth()->user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    @if(auth()->user()->role_id !== 4)
                    <li>
                        <a class="dropdown-item" href="{{ route('mahasiswa.profile') }}">
                            <span>Profil Saya</span>
                        </a>
                    </li>
                    @endif
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>