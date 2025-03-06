<x-layout bodyClass="g-sidenav-show bg-gray-200">
<x-navbars.sidebar activePage="materials" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Edit Materi" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Edit Materi</h6>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-2">
                            <form method="POST" action="{{ route('materials.update', $material) }}">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group input-group-outline my-3">
                                            <label class="form-label">Judul Materi</label>
                                            <input type="text" name="title" class="form-control" value="{{ $material->title }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input-group input-group-outline my-3">
                                            <label class="form-label">Konten</label>
                                            <textarea name="content" class="form-control" rows="10" required>{{ $material->content }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn bg-gradient-primary">Update</button>
                                <a href="{{ route('materials.index') }}" class="btn btn-outline-secondary">Batal</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-layout>