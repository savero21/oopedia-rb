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
                               class="nav-link {{ request()->routeIs('mahasiswa.materials*') && !request()->routeIs('mahasiswa.materials.questions*') ? 'active' : '' }}">
                                <i class="fas fa-book me-2"></i>
                                <span>Materi</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('mahasiswa.materials.questions.index') }}" 
                               class="nav-link {{ request()->routeIs('mahasiswa.materials.questions*') ? 'active' : '' }}">
                                <i class="fas fa-question-circle me-2"></i>
                                <span>Latihan Soal</span>
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
                               class="nav-link {{ request()->routeIs('mahasiswa.materials*') && !request()->routeIs('mahasiswa.materials.questions*') ? 'active' : '' }}">
                                <i class="fas fa-book me-2"></i>
                                <span>Materi</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('mahasiswa.materials.questions.index') }}" 
                               class="nav-link {{ request()->routeIs('mahasiswa.materials.questions*') ? 'active' : '' }}">
                                <i class="fas fa-question-circle me-2"></i>
                                <span>Latihan Soal</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Right side - Profile/Logout/Login/Register -->
        <div class="d-flex align-items-center">
            @if(auth()->user()->role_id === 4)
                <!-- Login and Register buttons for guest users -->
                <div class="me-3">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('guest-logout-login-form').submit();" 
                       class="btn btn-primary me-2">
                        <i class="fas fa-sign-in-alt me-1"></i> Login
                    </a>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('guest-logout-register-form').submit();" 
                       class="btn btn-primary">
                        <i class="fas fa-user-plus me-1"></i> Register
                    </a>
                </div>

                <!-- Hidden forms for guest logout and redirect -->
                <form id="guest-logout-login-form" action="{{ route('guest.logout') }}" method="POST" style="display: none;">
                    @csrf
                    <input type="hidden" name="redirect" value="{{ route('login') }}">
                </form>

                <form id="guest-logout-register-form" action="{{ route('guest.logout') }}" method="POST" style="display: none;">
                    @csrf
                    <input type="hidden" name="redirect" value="{{ route('register') }}">
                </form>
            @endif
            <div class="dropdown">
                <a class="nav-link dropdown-toggle profile-dropdown" href="#" role="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ asset('images/profile.gif') }}" alt="Profile" class="profile-image me-2">
                    <span class="me-2">{{ auth()->user()->role_id === 4 ? 'Tamu' : auth()->user()->name }}</span>
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
                        @if(auth()->user()->role_id === 4)
                            <form method="POST" action="{{ route('guest.logout') }}">
                                @csrf
                                <input type="hidden" name="redirect" value="{{ route('login') }}">
                                <button type="submit" class="dropdown-item">
                                    <span>Logout</span>
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <span>Logout</span>
                                </button>
                            </form>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>