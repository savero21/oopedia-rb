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
            // Pastikan form tidak kosong saat submit
            document.getElementById('materialForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Ambil nilai dari TinyMCE
                const content = tinymce.get('content-editor').getContent();
                const title = document.querySelector('input[name="title"]').value.trim();
                
                if (!title) {
                    alert('Judul materi tidak boleh kosong!');
                    return false;
                }
                
                if (!content) {
                    alert('Konten materi tidak boleh kosong!');
                    return false;
                }

                // Jika semua validasi passed, submit form
                this.submit();
            });

            // Disable tombol submit setelah diklik untuk mencegah double submit
            document.getElementById('submitBtn').addEventListener('click', function() {
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
            });
        });
    </script>
    @endpush
</x-layout>