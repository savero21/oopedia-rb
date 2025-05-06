@push('css')
<link href="{{ asset('css/mahasiswa.css') }}" rel="stylesheet">
@endpush

<nav class="navbar">
    <div class="container-fluid">
        <!-- Left side group -->
        <div class="d-flex align-items-center h-100">
            <!-- Logo -->
            <a class="navbar-brand me-4" href="{{ route('mahasiswa.dashboard') }}">
                <img src="{{ asset('images/logo.png') }}" alt="OOPEDIA" height="75">
            </a>
            
            <!-- Navigation links -->
            <div class="nav-links">
                <ul class="nav-menu">
                    <li>
                        <a href="{{ route('mahasiswa.dashboard') }}" 
                           class="nav-link {{ request()->routeIs('mahasiswa.dashboard*') ? 'active' : '' }}"
                           data-bs-toggle="tooltip" 
                           data-bs-placement="bottom" 
                           title="@auth Dashboard pengguna @else Dapat diakses setelah login @endauth">
                            <i class="fas fa-home me-2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('mahasiswa.materials.index') }}" 
                           class="nav-link {{ request()->routeIs('mahasiswa.materials*') && !request()->routeIs('mahasiswa.materials.questions*') ? 'active' : '' }}"
                           data-bs-toggle="tooltip" 
                           data-bs-placement="bottom" 
                           title="@auth Kumpulan materi pembelajaran @else Kumpulan materi pembelajaran @endauth">
                            <i class="fas fa-book me-2"></i>
                            <span>Materi</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('mahasiswa.materials.questions.index') }}" 
                           class="nav-link {{ request()->routeIs('mahasiswa.materials.questions*') ? 'active' : '' }}"
                           data-bs-toggle="tooltip" 
                           data-bs-placement="bottom" 
                           title="@auth Latihan soal untuk menguji pemahaman pengguna @else Dapat diakses setelah login @endauth">
                            <i class="fas fa-clipboard-check me-2"></i>
                            <span>Latihan Soal</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right side - Profile/Logout/Login/Register -->
        <div class="d-flex align-items-center">
            @guest
                <!-- Login and Register buttons - ONLY SHOWN FOR GUESTS -->
                <div class="me-3">
                    <a href="{{ route('login') }}" class="btn btn-primary me-2" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="bottom" 
                       title="Pengguna dapat mengakses semua fitur setelah login">
                        <i class="fas fa-sign-in-alt me-1"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="bottom" 
                       title="Buat akun baru untuk mengakses semua fitur">
                        <i class="fas fa-user-plus me-1"></i> Register
                    </a>
                </div>
            @endguest
            
            <div class="dropdown">
                <a class="nav-link dropdown-toggle profile-dropdown" href="#" role="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ asset('images/profile.gif') }}" alt="Profile" class="profile-image me-2">
                    <span class="me-2">
                        @auth
                            {{ auth()->user()->name }}
                        @else
                            Tamu
                        @endauth
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    @auth
                    <li>
                        <a class="dropdown-item" href="{{ route('mahasiswa.profile') }}">
                            <span>Profil Saya</span>
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                    @else
                    <li>
                        <form method="POST" action="{{ route('guest.logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <span>Keluar Mode Tamu</span>
                            </button>
                        </form>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Add this section to display the login reminder only for guests -->
@if(request()->routeIs('mahasiswa.dashboard*'))
<div class="container-fluid px-4 pt-3">

        @guest
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Silakan login untuk mengakses semua fitur pembelajaran
            </div>
        @endguest
        
        @auth
            <div class="welcome-message">
                <h4>Selamat datang, {{ auth()->user()->name }}!</h4>
                <p class="text-muted">Anda dapat mengakses semua materi dan latihan soal</p>
            </div>
        @endauth
    </div>
@endif

@push('scripts')
<script>
    // Inisialisasi tooltip Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush

