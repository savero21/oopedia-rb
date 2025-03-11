<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="students" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Data Mahasiswa" />
        <div class="container-fluid py-4">
            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.students.index') }}" class="mb-3">
                <div class="input-group input-group-outline my-3">
                    <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama..." value="{{ request('search') }}" style="height: 50px;">
                    <button class="btn btn-icon btn-3 btn-primary" type="submit" style="height: 50px;">
                        <span class="btn-inner--icon"><i class="material-icons">search</i></span>
                        <span class="btn-inner--text">Cari</span>
                    </button>
                </div>
            </form>

            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Data Mahasiswa</h6>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Mahasiswa</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Progress Keseluruhan</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($students as $student)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $student->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="height: 8px; width: 200px;">
                                                        <div class="progress-bar bg-gradient-success" 
                                                             role="progressbar" 
                                                             style="width: {{ $student->overall_progress }}%;" 
                                                             aria-valuenow="{{ $student->overall_progress }}" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <span class="text-xs font-weight-bold">{{ $student->overall_progress }}%</span>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <a href="{{ route('admin.students.progress', $student->id) }}" 
                                                   class="btn btn-sm btn-info">Detail Progress</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">
                                                <p class="text-sm mb-0">Belum ada data mahasiswa</p>
                                            </td>
                                        </tr>
                                        @endforelse
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