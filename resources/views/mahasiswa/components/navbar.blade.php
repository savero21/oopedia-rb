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
                    @if(auth()->user()->role_id === 3)
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
            @if(auth()->user()->role_id === 3)
                <span class="badge bg-primary text-white d-flex align-items-center guest-badge">
                    <i class="fas fa-user-clock me-2"></i>
                    <span>Mode Tamu</span>
                </span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline ms-3">
                    @csrf
                    <button type="submit" class="btn btn-link nav-link">
                        <i class="fas fa-sign-out-alt me-1"></i>
                        Logout
                    </button>
                </form>
            @else
                <!-- Regular user profile dropdown -->
                <div class="profile-section dropdown">
                    <a href="#" class="d-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ asset('images/profile.gif') }}" alt="Profile" class="rounded-circle" width="32">
                        <span class="ms-2 text-white">{{ auth()->user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('mahasiswa.profile') }}">Profile</a></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
</nav>