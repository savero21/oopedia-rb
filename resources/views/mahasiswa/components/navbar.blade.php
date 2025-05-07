@push('css')
<link href="{{ asset('css/mahasiswa.css') }}" rel="stylesheet">
<link href="https://unpkg.com/intro.js/minified/introjs.min.css" rel="stylesheet">
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
                           title="@auth Latihan soal untuk menguji pemahaman pengguna @else Latihan soal untuk menguji pemahaman @endauth">
                            <i class="fas fa-clipboard-check me-2"></i>
                            <span>Latihan Soal</span>
                            @guest
                                <small class="badge bg-warning text-dark ms-1">Terbatas</small>
                            @endguest
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right side - Profile/Logout/Login/Register -->
        <div class="d-flex align-items-center">
            @guest
                <div class="me-3">
                    <a href="{{ route('login') }}" class="btn btn-primary me-2" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="bottom" 
                       title="Login untuk akses semua soal latihan tanpa batasan">
                        <i class="fas fa-sign-in-alt me-1"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="bottom" 
                       title="Buat akun baru untuk akses semua soal latihan tanpa batasan">
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

@if(request()->routeIs('mahasiswa.dashboard*'))
<div class="container-fluid px-4 pt-3">
    @guest
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Silakan login untuk mengakses semua fitur pembelajaran
        </div>
    @endguest

    <!-- @auth
        <div class="welcome-message">
            <h4>Selamat datang, {{ auth()->user()->name }}!</h4>
            <p class="text-muted">Anda dapat mengakses semua materi dan latihan soal</p>
        </div>
    @endauth
</div>
@endif -->


@if(request()->routeIs('mahasiswa.dashboard*'))
<!-- <div class="container-fluid px-4 pt-3">
    @guest
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Silakan login untuk mengakses semua fitur pembelajaran
        </div>
    @endguest -->

    @auth
        <div class="welcome-message">
            <h4>Selamat datang, {{ auth()->user()->name }}!</h4>
            <p class="text-muted">Anda dapat mengakses semua materi dan latihan soal</p>
        </div>
    @endauth
</div>
@endif
@push('scripts')
<script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>
<script>
    // Simpan URL route
    const routeLogin = "{{ route('login') }}";
    const routeRegister = "{{ route('register') }}";
    const routeDashboard = "{{ route('mahasiswa.dashboard') }}";
    const routeMateri = "{{ route('mahasiswa.materials.index') }}";
    const routeSoal = "{{ route('mahasiswa.materials.questions.index') }}";
    const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};

    // Variabel untuk menandai klik sidebar
    let sidebarClicked = false;

    document.addEventListener('DOMContentLoaded', function () {
        // Inisialisasi tooltip
        var tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltips.map(function(el) {
            return new bootstrap.Tooltip(el);
        });

        // Jika bukan dari klik sidebar, jalankan tour
        if (!sidebarClicked && !sessionStorage.getItem('skip_tour')) {
            startTutorial();
        }
    });

    // Fungsi untuk memulai tutorial
    function startTutorial() {
        let steps = [
            {
                intro: "Halo! Mari kita mulai dengan mengenal tampilan website ini."
            }
        ];

        if (!isLoggedIn) {
            steps.push(
                {
                    element: document.querySelector('a.btn[href="' + routeLogin + '"]'),
                    intro: "Klik tombol Login ini untuk masuk ke akun Anda"
                },
                {
                    element: document.querySelector('a.btn[href="' + routeRegister + '"]'),
                    intro: "Atau klik tombol Register untuk membuat akun baru"
                }
            );
        }

        steps.push(
            {
                element: document.querySelector('.nav-link[href="' + routeDashboard + '"]'),
                intro: "Ini adalah dashboard. Kamu bisa melihat ringkasan aktivitas di sini."
            },
            {
                element: document.querySelector('.nav-link[href="' + routeMateri + '"]'),
                intro: "Di sini kamu bisa belajar berbagai materi pembelajaran."
            },
            {
                element: document.querySelector('.nav-link[href="' + routeSoal + '"]'),
                intro: "Cek pemahamanmu di bagian latihan soal ini!"
            },
            {
                intro: "Siap menjelajah? Klik di mana saja untuk menyelesaikan tutorial ini!"
            }
        );

        introJs().setOptions({
            steps: steps,
            showProgress: true,
            exitOnOverlayClick: true,
            showBullets: false,
            scrollToElement: true,
            nextLabel: 'Berikutnya',
            prevLabel: 'Sebelumnya',
            doneLabel: 'Selesai'
        }).start();
    }

    // Tambahkan event listener untuk semua link di sidebar
    document.querySelectorAll('.sidebar a').forEach(link => {
        link.addEventListener('click', function(event) {
            // Tandai bahwa ini klik dari sidebar
            sidebarClicked = true;
            
            // Untuk latihan soal, tetap arahkan ke login jika tamu
            if (this.href.includes('/questions') && !isLoggedIn) {
                event.preventDefault();
                alert('Silakan login untuk mengakses latihan soal');
                window.location.href = "{{ route('login') }}";
                return;
            }
            
            // Untuk materi, biarkan lanjut tanpa tour
            sessionStorage.setItem('skip_tour', 'true');
        });
    });
</script>
@endpush