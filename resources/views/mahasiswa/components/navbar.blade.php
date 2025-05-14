@push('css')
<link href="{{ asset('css/mahasiswa.css') }}" rel="stylesheet">
<link href="https://unpkg.com/intro.js/minified/introjs.min.css" rel="stylesheet">
@endpush


<nav class="navbar">
    <div class="container-fluid">
        <!-- Left side group -->
        <div class="d-flex align-items-center h-100">
            <!-- Navigation links -->
            <div class="nav-links">
                <ul class="nav-menu">
                    @auth
                    <li>
                        <a href="{{ route('mahasiswa.dashboard') }}" 
                           class="nav-link {{ request()->routeIs('mahasiswa.dashboard*') ? 'active' : '' }}"
                           data-bs-toggle="tooltip" 
                           data-bs-placement="bottom" 
                           title="Dashboard pengguna">
                            <i class="fas fa-home me-2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    @endauth
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
        <div class="d-flex align-items-center ms-auto">
            @guest
                <div class="auth-buttons me-3 d-none d-md-flex">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm me-2" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="bottom" 
                       title="Login untuk akses semua soal latihan tanpa batasan">
                        <i class="fas fa-sign-in-alt me-1"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="bottom" 
                       title="Buat akun baru untuk akses semua soal latihan tanpa batasan">
                        <i class="fas fa-user-plus me-1"></i> Register
                    </a>
                </div>
            @endguest
            
            @auth
            <div class="dropdown profile-dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ asset('images/profile.gif') }}" alt="Profile" class="profile-image me-2" width="30" height="30">
                    <span class="profile-name d-none d-sm-inline">
                        {{ auth()->user()->name }}
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
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
                </ul>
            </div>
            @endauth
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

        // Only show tutorial for authenticated users on dashboard page and only once
        const isMainTutorialCompleted = sessionStorage.getItem('main_tutorial_complete');
        const isDashboardPage = {{ request()->routeIs('mahasiswa.dashboard*') ? 'true' : 'false' }};
        const isQuestionsPage = {{ request()->routeIs('mahasiswa.materials.questions*') ? 'true' : 'false' }};
        
        // Skip tutorial for guests and on question pages
        if (isLoggedIn && !isMainTutorialCompleted && isDashboardPage && !sidebarClicked && !sessionStorage.getItem('skip_tour')) {
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

        // Add dashboard tutorial step only for logged in users
        if (isLoggedIn) {
            steps.push({
                element: document.querySelector('.nav-link[href="' + routeDashboard + '"]'),
                intro: "Ini adalah dashboard. Kamu bisa melihat ringkasan aktivitas di sini."
            });
        }
        
        // These steps are always shown
        steps.push(
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
        }).oncomplete(function() {
            // Mark main tutorial as completed
            sessionStorage.setItem('main_tutorial_complete', 'true');
        }).start();
    }

    // Tambahkan event listener untuk semua link di sidebar
    document.querySelectorAll('.sidebar a').forEach(link => {
        link.addEventListener('click', function(event) {
            // Tandai bahwa ini klik dari sidebar
            sidebarClicked = true;
            
            // Untuk materi, biarkan lanjut tanpa tour
            sessionStorage.setItem('skip_tour', 'true');
        });
    });
</script>
@endpush