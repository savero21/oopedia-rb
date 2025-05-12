<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="materials" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Materi" />
        <div class="container-fluid py-4">
            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.materials.index') }}" class="mb-3">
                <div class="input-group input-group-outline my-3">
                    <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}" style="height: 50px;">
                    <button class="btn btn-icon btn-3 btn-primary" type="submit" style="height: 50px;">
                        <span class="btn-inner--icon"><i class="material-icons">search</i></span>
                        <span class="btn-inner--text">Cari</span>
                    </button>
                </div>
            </form>
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                    <br><br>

                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <div class="row">
                                    <div class="col-6">
                                        <h6 class="text-white text-capitalize ps-3">Daftar Materi</h6>
                                    </div>
                                    <div class="col-6 text-end">
                                        <a href="{{ route('admin.materials.create') }}" class="btn btn-sm btn-light me-3">
                                            <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Tambah Materi
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                <a href="{{ route('admin.materials.index', ['sort' => 'title', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-secondary">
                                                    Judul {!! request('sort') == 'title' ? (request('direction') == 'asc' ? '↑' : '↓') : '' !!}
                                                </a>
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Dibuat Oleh</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                <a href="{{ route('admin.materials.index', ['sort' => 'created_at', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-secondary">
                                                    Tanggal {!! request('sort') == 'created_at' ? (request('direction') == 'asc' ? '↑' : '↓') : '' !!}
                                                </a>
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($materials as $material)
                                        <tr>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs font-weight-bold">{{ $loop->iteration }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $material->title }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $material->creator ? $material->creator->name : 'No Creator' }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $material->created_at->format('d/m/Y') }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <a href="{{ route('admin.materials.edit', $material->id) }}" class="btn btn-sm btn-info">Edit</a>
                                                <form action="{{ route('admin.materials.destroy', $material->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus materi ini?')">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-layout>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cek apakah tutorial halaman materi sudah pernah ditampilkan
        const isMaterialTutorialCompleted = localStorage.getItem('admin_material_tutorial_complete');
        
        // Tampilkan tutorial jika belum pernah ditampilkan
        if (!isMaterialTutorialCompleted && !localStorage.getItem('skip_admin_tour')) {
            startMaterialTutorial();
        }
    });
    
    function startMaterialTutorial() {
        const steps = [
            {
                intro: "Selamat datang di halaman Manajemen Materi!"
            },
            {
                element: document.querySelector('form.mb-3'),
                intro: "Gunakan form pencarian ini untuk menemukan materi berdasarkan judul atau konten."
            },
            {
                element: document.querySelector('a[href="{{ route("admin.materials.create") }}"]'),
                intro: "Klik tombol ini untuk menambahkan materi baru."
            },
            {
                element: document.querySelector('table.table'),
                intro: "Tabel ini menampilkan semua materi yang tersedia. Anda dapat mengedit atau menghapus materi dari sini."
            }
        ];
        
        introJs().setOptions({
            steps: steps,
            showProgress: true,
            exitOnOverlayClick: true,
            showBullets: false,
            scrollToElement: true,
            nextLabel: 'Berikutnya',
            prevLabel: 'Sebelumnya',
            doneLabel: 'Selesai',
            tooltipClass: 'customTooltip'
        }).oncomplete(function() {
            localStorage.setItem('admin_material_tutorial_complete', 'true');
        }).onexit(function() {
            localStorage.setItem('admin_material_tutorial_complete', 'true');
        }).start();
    }
</script>
@endpush