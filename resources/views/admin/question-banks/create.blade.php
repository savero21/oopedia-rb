<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="question-banks" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Tambah Bank Soal" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                    <br><br>

                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Tambah Bank Soal Baru</h6>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <form method="POST" action="{{ route('admin.question-banks.store') }}" class="p-4">
                                @csrf
                                
                                @if($errors->any())
                                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                        @foreach($errors->all() as $error)
                                            {{ $error }}<br>
                                        @endforeach
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Bank Soal</label>
                                            <div class="input-group input-group-outline">
                                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                            </div>
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Deskripsi</label>
                                            <div class="input-group input-group-outline">
                                                <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                                            </div>
                                            @error('description')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="{{ route('admin.question-banks.index') }}" class="btn btn-outline-secondary">Batal</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-layout>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (!localStorage.getItem('admin_question_bank_create_tutorial_complete')) {
            setTimeout(startQuestionBankCreateTutorial, 500);
        }
    });

    function startQuestionBankCreateTutorial() {
        const steps = [
            {
                intro: "Selamat datang di halaman pembuatan bank soal!"
            },
            {
                element: document.querySelector('input[name="name"]'),
                intro: "Masukkan nama bank soal di sini."
            },
            {
                element: document.querySelector('select[name="material_id"]'),
                intro: "Pilih materi yang terkait dengan bank soal ini."
            },
            {
                element: document.querySelector('select[name="difficulty"]'),
                intro: "Tentukan tingkat kesulitan untuk bank soal ini."
            },
            {
                element: document.querySelector('button[type="submit"]'),
                intro: "Klik tombol ini untuk membuat bank soal baru."
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
            localStorage.setItem('admin_question_bank_create_tutorial_complete', 'true');
        }).start();
    }
</script>
@endpush 