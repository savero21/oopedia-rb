<div class="sidebar">
    <div class="sidebar-header">
        <h5 class="sidebar-title">
            @if(request()->routeIs('mahasiswa.dashboard*'))
                Dashboard 
            @elseif(request()->routeIs('mahasiswa.profile'))
                Profil
            @elseif(request()->routeIs('mahasiswa.materials*') && !request()->routeIs('mahasiswa.materials.questions*'))
                Daftar Materi
            @elseif(request()->routeIs('mahasiswa.materials.questions*'))
                Latihan Soal
            @elseif(request()->routeIs('mahasiswa.ueq.create'))
                UEQ Survey
            @else
                Pembelajaran
            @endif
        </h5>
    </div>

    @if(request()->routeIs('mahasiswa.profile') && auth()->check())
        {{-- Show only Dashboard and Profile menu when on profile page (except for guests) --}}
        <ul class="nav-menu">
            <li>
                <a href="{{ route('mahasiswa.dashboard') }}"
                   class="menu-item {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}"
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Lihat statistik dan progres pembelajaran Anda">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('mahasiswa.profile') }}" 
                   class="menu-item {{ request()->routeIs('mahasiswa.profile') ? 'active' : '' }}"
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Lihat dan ubah profil Anda">
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
                   class="menu-item {{ request()->routeIs('mahasiswa.dashboard') && !request()->routeIs('mahasiswa.dashboard.*') ? 'active' : '' }}"
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Lihat ringkasan progres pembelajaran Anda">
                    <i class="fas fa-home"></i>
                    <span>Beranda</span>
                </a>
            </li>
            <li>
                <a href="{{ route('mahasiswa.dashboard.in-progress') }}" 
                   class="menu-item {{ request()->routeIs('mahasiswa.dashboard.in-progress') ? 'active' : '' }}"
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Lihat materi yang sedang Anda pelajari">
                    <i class="fas fa-spinner"></i>
                    <span>Sedang Dipelajari</span>
                </a>
            </li>
            <li>
                <a href="{{ route('mahasiswa.dashboard.completed') }}" 
                   class="menu-item {{ request()->routeIs('mahasiswa.dashboard.completed') ? 'active' : '' }}"
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Lihat materi yang telah Anda selesaikan">
                    <i class="fas fa-check-circle"></i>
                    <span>Selesai</span>
                </a>
            </li>
        </ul>
    @elseif(request()->routeIs('mahasiswa.ueq.create'))
        {{-- UEQ Survey Sidebar Menu --}}
        <ul class="nav-menu">
            <li>
                <a href="{{ route('mahasiswa.dashboard') }}" 
                   class="menu-item"
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Kembali ke dashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
        </ul>
    @elseif(request()->routeIs('mahasiswa.materials*') && !request()->routeIs('mahasiswa.materials.questions*'))
        {{-- Hanya tampilkan daftar materi ketika di halaman materi --}}
        <ul class="nav-menu">
            <li>
                <a href="{{ route('mahasiswa.materials.index') }}" 
                   class="menu-item {{ request()->is('mahasiswa/materials') ? 'active' : '' }}"
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Lihat semua materi pembelajaran">
                    <i class="fas fa-list"></i>
                    <span>Semua Materi</span>
                </a>
            </li>
        </ul>
        
        {{-- Materi PBO Section Divider --}}
        <div class="sidebar-header mt-3">
            <h5 class="sidebar-title">Materi PBO</h5>
        </div>
        
        <ul class="nav-menu">
            @if(isset($materials))
                @foreach($materials as $m)
                    <li class="materi-item {{ request()->segment(3) == (is_array($m) ? $m['material']->id : $m->id) ? 'active' : '' }}">
                        <a href="{{ route('mahasiswa.materials.show', is_array($m) ? $m['material']->id : $m->id) }}"
                           class="menu-item {{ request()->segment(3) == (is_array($m) ? $m['material']->id : $m->id) ? 'active' : '' }}"
                           data-bs-toggle="tooltip"
                           data-bs-placement="right"
                           title="Pelajari materi {{ is_array($m) ? $m['material']->title : $m->title }}">
                            <i class="fas fa-book"></i>
                            <span>{{ is_array($m) ? $m['material']->title : $m->title }}</span>
                        </a>
                    </li>
                @endforeach
            @endif
        </ul>
    @elseif(request()->routeIs('mahasiswa.materials.questions*'))
        {{-- Sidebar untuk Latihan Soal --}}
        <ul class="nav-menu">
            <li>
                <a href="{{ route('mahasiswa.materials.questions.index') }}" 
                   class="menu-item {{ request()->is('mahasiswa/materials/questions') ? 'active' : '' }}"
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Lihat daftar latihan soal per materi">
                    <i class="fas fa-list"></i>
                    <span>Daftar Latihan Soal</span>
                </a>
            </li>
        </ul>

        {{-- Daftar Materi --}}
        <div class="sidebar-header mt-3">
            <h5 class="sidebar-title">Materi</h5>
        </div>

        <ul class="nav-menu">
            @if(isset($materials))
                @foreach($materials as $m)
                    @php
                        // Helper to determine correct way to access the material
                        $materialItem = is_array($m) && isset($m['material']) ? $m['material'] : $m;
                    @endphp
                    <li>
                        <a href="{{ route('mahasiswa.materials.questions.levels', ['material' => $materialItem->id, 'difficulty' => 'beginner']) }}"
                           class="menu-item {{ request()->segment(3) == $materialItem->id ? 'active' : '' }}"
                           data-bs-toggle="tooltip"
                           data-bs-placement="right"
                           title="Latihan soal untuk materi {{ $materialItem->title }}">
                            <i class="fas fa-folder-open"></i>
                            <span>{{ $materialItem->title }}</span>
                        </a>
                        
                        @if(request()->segment(3) == $materialItem->id)
                            <div class="difficulty-menu">
                                <a href="{{ route('mahasiswa.materials.questions.levels', ['material' => $materialItem->id, 'difficulty' => 'beginner']) }}"
                                   class="menu-item sub-menu-item {{ request()->query('difficulty') == 'beginner' || request()->query('difficulty') == null ? 'active' : '' }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="Soal tingkat pemula">
                                    <i class="fas fa-star beginner-star"></i>
                                    <span>Beginner</span>
                                </a>
                                
                                <a href="{{ route('mahasiswa.materials.questions.levels', ['material' => $materialItem->id, 'difficulty' => 'medium']) }}"
                                   class="menu-item sub-menu-item {{ request()->query('difficulty') == 'medium' ? 'active' : '' }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="Soal tingkat menengah">
                                    <i class="fas fa-star medium-star"></i>
                                    <span>Medium</span>
                                </a>
                                
                                <a href="{{ route('mahasiswa.materials.questions.levels', ['material' => $materialItem->id, 'difficulty' => 'hard']) }}"
                                   class="menu-item sub-menu-item {{ request()->query('difficulty') == 'hard' ? 'active' : '' }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="Soal tingkat sulit">
                                    <i class="fas fa-star hard-star"></i>
                                    <span>Hard</span>
                                </a>
                            </div>
                        @endif
                    </li>
                @endforeach
            @endif
        </ul>
    @else
        {{-- Default Sidebar Menu --}}
        <ul class="nav-menu">
            <li>
                <a href="{{ route('mahasiswa.dashboard') }}" 
                   class="menu-item {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}"
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Lihat statistik dan progres pembelajaran Anda">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('mahasiswa.materials.index') }}" 
                   class="menu-item {{ request()->routeIs('mahasiswa.materials.index') ? 'active' : '' }}"
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Akses materi pembelajaran PBO">
                    <i class="fas fa-book"></i>
                    <span>Materi</span>
                </a>
            </li>
            <li>
                <a href="{{ route('mahasiswa.materials.questions.index') }}" 
                   class="menu-item {{ request()->routeIs('mahasiswa.materials.questions.index') ? 'active' : '' }}"
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Uji pemahaman Anda dengan latihan soal">
                    <i class="fas fa-question-circle"></i>
                    <span>Latihan Soal</span>
                </a>
            </li>
            @auth
                <li>
                    <a href="{{ route('mahasiswa.profile') }}" 
                       class="menu-item {{ request()->routeIs('mahasiswa.profile') ? 'active' : '' }}"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="right" 
                       title="Lihat dan ubah profil Anda">
                        <i class="fas fa-user"></i>
                        <span>Profil Saya</span>
                    </a>
                </li>
            @endauth
            <li>
                <a href="{{ route('mahasiswa.ueq.create') }}" 
                   class="menu-item {{ request()->routeIs('mahasiswa.ueq.create') ? 'active' : '' }}"
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Berikan feedback tentang sistem">
                    <i class="fas fa-poll"></i>
                    <span>UEQ Survey</span>
                </a>
            </li>
        </ul>
    @endif

    {{-- Leaderboard Section Divider --}}
    <div class="sidebar-header mt-4">
        <h5 class="sidebar-title">Leaderboard</h5>
    </div>
    
    {{-- Leaderboard Menu Item --}}
    <ul class="nav-menu">
        <li>
            @auth
                <a href="{{ route('mahasiswa.leaderboard') }}" 
                   class="menu-item {{ request()->routeIs('mahasiswa.leaderboard') ? 'active' : '' }}"
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Lihat peringkat pengguna">
                    <i class="fas fa-trophy"></i>
                    <span>Peringkat</span>
                </a>
            @else
                <a href="#" 
                   class="menu-item"
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Silakan login untuk melihat peringkat">
                    <i class="fas fa-trophy"></i>
                    <span>Peringkat</span>
                    <span class="badge bg-warning text-dark ms-2">Perlu Login</span>
                </a>
            @endauth
        </li>
    </ul>

    {{-- UEQ Survey Section Divider (only for logged-in students) --}}
    @auth
        @if(auth()->user()->role_id == 3)
        <div class="sidebar-header mt-4">
            <h5 class="sidebar-title">Feedback</h5>
        </div>
        
        <ul class="nav-menu">
            <li>
                <a href="{{ route('mahasiswa.ueq.create') }}" 
                   class="menu-item {{ request()->routeIs('mahasiswa.ueq.create') ? 'active' : '' }}"
                   data-bs-toggle="tooltip" 
                   data-bs-placement="right" 
                   title="Berikan feedback tentang sistem">
                    <i class="fas fa-poll"></i>
                    <span>UEQ Survey</span>
                </a>
            </li>
        </ul>
        @endif
    @endauth
</div>

@push('css')
<style>
    /* Additional styles for the disabled menu items */
    .menu-item.disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .badge-login-required {
        font-size: 0.7rem;
        margin-left: 8px;
    }
</style>
@endpush