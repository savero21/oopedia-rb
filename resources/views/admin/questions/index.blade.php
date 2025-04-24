<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="questions" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Soal dan Jawaban" />
        <div class="container-fluid py-4">
            <!-- Search Form -->
            <form method="GET" action="{{ $material ? route('admin.materials.questions.index', $material) : route('admin.questions.index') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group input-group-outline my-3">
                            <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan soal, tipe soal, atau pembuat..." value="{{ request('search') }}" style="height: 50px;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-outline my-3">
                            <select name="difficulty" class="form-control" style="height: 50px;">
                                <option value="">Semua Tingkat Kesulitan</option>
                                <option value="beginner" {{ request('difficulty') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-icon btn-3 btn-primary" type="submit" style="height: 50px;">
                            <span class="btn-inner--icon"><i class="material-icons">search</i></span>
                            <span class="btn-inner--text">Cari</span>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Questions Table -->    
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                    <br><br>
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3">
                                    {{ $material ? 'Soal untuk Materi: ' . $material->title : 'Daftar Soal' }}
                                </h6>
                                @if($material)
                                    <a href="{{ route('admin.materials.questions.create', $material) }}" class="btn btn-sm btn-light me-3">Tambah Soal</a>
                                @else
                                    <a href="{{ route('admin.questions.create') }}" class="btn btn-sm btn-light me-3">Tambah Soal</a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Materi</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Pertanyaan</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tipe Soal</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kesulitan</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Pembuat</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($questions as $question)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $question->material->title }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $question->question_text }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $question->formatted_type }}</p>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $question->difficulty == 'beginner' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($question->difficulty) }}
                                                </span>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $question->createdBy->name }}</p>
                                            </td>
                                            <td class="align-middle text-center">
                                                @if($material)
                                                    <a href="{{ route('admin.materials.questions.edit', ['material' => $material, 'question' => $question]) }}" class="btn btn-sm btn-info">Edit</a>
                                                    <form action="{{ route('admin.materials.questions.destroy', ['material' => $material, 'question' => $question]) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus soal ini?')">Hapus</button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('admin.questions.edit', $question) }}" class="btn btn-sm btn-info">Edit</a>
                                                    <form action="{{ route('admin.questions.destroy', $question) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus soal ini?')">Hapus</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                        <!-- Answers Section -->
                                        <tr>
                                            <td colspan="5">
                                                <div class="ms-4">
                                                    <strong class="text-xs">Jawaban:</strong>
                                                    <ul class="list-unstyled ms-3">
                                                        @foreach($question->answers as $answer)
                                                        <li class="text-xs {{ $answer->is_correct ? 'text-success' : '' }}">
                                                            <strong>{{ $answer->answer_text }}</strong>
                                                            @if($answer->is_correct)
                                                                <span class="badge bg-success">Benar</span>
                                                            @endif
                                                            @if($answer->explanation)
                                                                <br>
                                                                <small class="text-muted">Penjelasan: {{ $answer->explanation }}</small>
                                                            @endif
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
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
