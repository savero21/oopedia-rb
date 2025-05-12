<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="students" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Tambahkan Data Mahasiswa" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <br><br>
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Tambahkan Data Mahasiswa</h6>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="p-4">
                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                @if($errors->any())
                                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                        @foreach($errors->all() as $error)
                                            {{ $error }}<br>
                                        @endforeach
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <div class="mb-4">
                                    <h5>Petunjuk Tambah Data:</h5>
                                    <ol>
                                        <li>File harus dalam format Excel (.xlsx, .xls) atau CSV (.csv)</li>
                                        <li>File harus memiliki kolom: name, email, password</li>
                                        <li>Mahasiswa yang ditambahkan akan otomatis disetujui</li>
                                    </ol>
                                    
                                    <div class="mt-3">
                                        <a href="{{ route('admin.students.download-template') }}" class="btn btn-sm btn-info">
                                            <i class="material-icons text-sm">download</i> Download Template
                                        </a>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('admin.students.process-import') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">File Excel/CSV</label>
                                                <div class="input-group input-group-outline">
                                                    <input type="file" name="excel_file" class="form-control" required accept=".xlsx,.xls,.csv">
                                                </div>
                                                @error('excel_file')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="material-icons text-sm">upload_file</i> Tambahkan
                                            </button>
                                            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">Batal</a>
                                        </div>
                                    </div>
                                </form>

                                <div class="mt-4">
                                    <h5>Informasi File:</h5>
                                    <ul>
                                        <li>Maksimal Ukuran File: {{ (int)(ini_get('upload_max_filesize')) }} MB</li>
                                    </ul>
                                </div>
                                
                                @if(session('importErrors'))
                                    <div class="mt-4">
                                        <h5>Error pada baris:</h5>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Baris</th>
                                                        <th>Error</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach(session('importErrors') as $error)
                                                        <tr>
                                                            <td>{{ $error['row'] }}</td>
                                                            <td>
                                                                <ul class="mb-0">
                                                                    @foreach($error['errors'] as $message)
                                                                        <li>{{ $message }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
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
        // Cek apakah tutorial halaman import mahasiswa sudah pernah ditampilkan
        const isStudentImportTutorialCompleted = localStorage.getItem('admin_student_import_tutorial_complete');
        
        // Tampilkan tutorial jika belum pernah ditampilkan
        if (!isStudentImportTutorialCompleted && !localStorage.getItem('skip_admin_tour')) {
            setTimeout(startStudentImportTutorial, 500);
        }
    });
    
    function startStudentImportTutorial() {
        const steps = [
            {
                intro: "Selamat datang di halaman Import Data Mahasiswa!"
            },
            {
                element: document.querySelector('.card-body p'),
                intro: "Bagian ini menjelaskan format file yang dibutuhkan untuk mengimpor data mahasiswa."
            },
            {
                element: document.querySelector('a[href*="template"]'),
                intro: "Klik tombol ini untuk mengunduh template file Excel yang dapat Anda isi dengan data mahasiswa."
            },
            {
                element: document.querySelector('input[type="file"]'),
                intro: "Klik di sini untuk memilih file Excel/CSV yang berisi data mahasiswa yang akan diimpor."
            },
            {
                element: document.querySelector('button[type="submit"]'),
                intro: "Setelah memilih file, klik tombol ini untuk mulai mengimpor data mahasiswa."
            },
            {
                intro: "Pastikan format data dalam file sesuai dengan template untuk menghindari kesalahan saat impor."
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
            localStorage.setItem('admin_student_import_tutorial_complete', 'true');
        }).onexit(function() {
            localStorage.setItem('admin_student_import_tutorial_complete', 'true');
        }).start();
    }
</script>
@endpush 