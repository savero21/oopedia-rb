<div class="sidebar">
    <div class="sidebar-header">
        <h5 class="sidebar-title">
            @if(request()->routeIs('mahasiswa.dashboard*'))
                Ringkasan Progress
            @elseif(request()->routeIs('mahasiswa.profile'))
                Profil
            @elseif(request()->routeIs('mahasiswa.materials*') && !request()->routeIs('mahasiswa.materials.questions*'))
                Daftar Materi
            @elseif(request()->routeIs('mahasiswa.materials.questions*'))
                Latihan Soal
            @else
                Pembelajaran
            @endif
        </h5>
    </div>

    @if(request()->routeIs('mahasiswa.profile') && auth()->user()->role_id !== 4)
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
    @elseif(request()->routeIs('mahasiswa.dashboard*') && auth()->user()->role_id !== 4)
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
        <ul class="nav-menu">
            <li>
                <a href="{{ route('mahasiswa.materials.index') }}" 
                   class="menu-item {{ request()->is('mahasiswa/materials') && !request()->routeIs('mahasiswa.materials.questions*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>Daftar Materi</span>
                </a>
            </li>
            <li>
                <a href="{{ route('mahasiswa.materials.questions.index') }}" 
                   class="menu-item {{ request()->routeIs('mahasiswa.materials.questions*') && !request()->segment(4) ? 'active' : '' }}">
                    <i class="fas fa-question-circle"></i>
                    <span>Latihan Soal</span>
                </a>
            </li>
        </ul>
        
        {{-- Materi PBO Section Divider --}}
        <div class="sidebar-header mt-3">
            <h5 class="sidebar-title">Materi PBO</h5>
        </div>
        
        <ul class="nav-menu">
            @foreach($materials as $m)
                <li>
                    <a href="{{ route('mahasiswa.materials.show', $m->id) }}" 
                       class="menu-item {{ request()->is('mahasiswa/materials/'.$m->id) && !request()->routeIs('mahasiswa.materials.questions*') ? 'active' : '' }}">
                        <i class="fas fa-book"></i>
                        <span>{{ $m->title }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        {{-- Tampilkan menu soal jika sedang di halaman soal untuk materi tertentu ATAU sedang melihat materi tertentu --}}
        @if((request()->is('mahasiswa/materials/*/questions*') && request()->segment(4)) || 
            (request()->routeIs('mahasiswa.materials.show') && request()->segment(3)) ||
            (request()->routeIs('mahasiswa.materials.questions.review') && request()->segment(4)))
            @php
                // Ambil ID materi dari URL
                $currentMaterialId = request()->segment(3);
                $currentMaterial = $materials->firstWhere('id', $currentMaterialId);
            @endphp
            
            @if($currentMaterial)
                <div class="sidebar-header mt-3">
                    <h5 class="sidebar-title">Soal: {{ $currentMaterial->title }}</h5>
                </div>
                
                <ul class="nav-menu">
                    <li>
                        <a href="{{ route('mahasiswa.materials.questions.show', ['material' => $currentMaterialId, 'difficulty' => 'beginner']) }}" 
                           class="menu-item {{ request()->routeIs('mahasiswa.materials.questions.show') && (request()->query('difficulty') == 'beginner' || !request()->query('difficulty')) ? 'active' : '' }}">
                            <i class="fas fa-star text-success"></i>
                            <span>Beginner</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('mahasiswa.materials.questions.show', ['material' => $currentMaterialId, 'difficulty' => 'medium']) }}" 
                           class="menu-item {{ request()->routeIs('mahasiswa.materials.questions.show') && request()->query('difficulty') == 'medium' ? 'active' : '' }}">
                            <i class="fas fa-star text-warning"></i>
                            <span>Medium</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('mahasiswa.materials.questions.show', ['material' => $currentMaterialId, 'difficulty' => 'hard']) }}" 
                           class="menu-item {{ request()->routeIs('mahasiswa.materials.questions.show') && request()->query('difficulty') == 'hard' ? 'active' : '' }}">
                            <i class="fas fa-star text-danger"></i>
                            <span>Hard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('mahasiswa.materials.questions.review', ['material' => $currentMaterialId]) }}" 
                           class="menu-item {{ request()->routeIs('mahasiswa.materials.questions.review') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-check text-primary"></i>
                            <span>Review Soal</span>
                        </a>
                    </li>
                </ul>
            @endif
        @endif
    @endif

    {{-- Leaderboard Section Divider --}}
    <div class="sidebar-header mt-4">
        <h5 class="sidebar-title">Leaderboard</h5>
    </div>
    
    {{-- Leaderboard Menu Item --}}
    <ul class="nav-menu">
        <li>
            <a href="{{ route('mahasiswa.leaderboard') }}" 
               class="menu-item {{ request()->routeIs('mahasiswa.leaderboard') ? 'active' : '' }}">
                <i class="fas fa-trophy"></i>
                <span>Peringkat</span>
            </a>
        </li>
    </ul>
</div>