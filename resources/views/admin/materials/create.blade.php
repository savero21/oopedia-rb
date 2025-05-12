<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <!-- Add TinyMCE configuration component in the head section -->
    @push('head')
        <x-head.tinymce-config />
    @endpush

    <x-navbars.sidebar activePage="materials" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Tambah Materi" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                    <br><br>

                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Tambah Materi Baru</h6>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-2">
                            <form method="POST" action="{{ route('admin.materials.store') }}" id="materialForm">
                                @csrf
                                <input type="hidden" name="created_by" value="{{ auth()->id() }}">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Judul Materi</label>
                                            <div class="input-group input-group-outline">
                                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" required value="{{ old('title') }}">
                                            </div>
                                            @error('title')
                                                <div class="text-danger text-xs">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Isi Materi</label>
                                            <div class="my-3">
                                                <textarea id="content-editor" name="content" class="@error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                                            </div>
                                            @error('content')
                                                <div class="text-danger text-xs">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <button type="submit" class="btn bg-gradient-primary" id="submitBtn">
                                            <span class="btn-inner--text">Simpan</span>
                                        </button>
                                        <a href="{{ route('admin.materials.index') }}" class="btn btn-outline-secondary">Batal</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (!localStorage.getItem('admin_material_create_tutorial_complete')) {
                setTimeout(startMaterialCreateTutorial, 500);
            }
        });

        function startMaterialCreateTutorial() {
            const steps = [
                {
                    intro: "Selamat datang di halaman pembuatan materi baru!"
                },
                {
                    element: document.querySelector('input[name="title"]'),
                    intro: "Masukkan judul materi pembelajaran di sini."
                },
                {
                    element: document.querySelector('input[name="description"]'),
                    intro: "Berikan deskripsi singkat tentang materi ini."
                },
                {
                    element: document.querySelector('.tox-tinymce'),
                    intro: "Gunakan editor ini untuk menulis konten materi. Anda dapat menambahkan teks, gambar, dan kode program."
                },
                {
                    element: document.querySelector('button[type="submit"]'),
                    intro: "Setelah selesai, klik tombol ini untuk menyimpan materi."
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
                localStorage.setItem('admin_material_create_tutorial_complete', 'true');
            }).start();
        }
    </script>
    @endpush
</x-layout>