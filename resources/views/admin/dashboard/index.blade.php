<x-layout bodyClass="dashboard-layout g-sidenav-show">
    <x-navbars.sidebar activePage="dashboard" :userName="$userName" :userRole="$userRole"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Dashboard Admin"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <!-- Statistics Cards Row -->
            <div class="row">
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl">
                                <i class="material-icons opacity-10">group</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Mahasiswa</p>
                                <h4 class="mb-0">{{ $totalStudents }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl">
                                <i class="material-icons opacity-10">person_outline</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Mahasiswa Aktif</p>
                                <h4 class="mb-0">{{ $activeStudents }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl">
                                <i class="material-icons opacity-10">library_books</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Materi</p>
                                <h4 class="mb-0">{{ $totalMaterials }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl">
                                <i class="material-icons opacity-10">quiz</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Soal</p>
                                <h4 class="mb-0">{{ $totalQuestions }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Materials Section -->
            <div class="row mt-4">
                <div class="col-lg-12 mb-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>Progress Mahasiswa</h6>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mahasiswa</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Soal Diselesaikan</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Progress Materi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($studentProgress as $student)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $student->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs font-weight-bold">{{ $student->completed_questions }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <span class="me-2 text-xs font-weight-bold">{{ $student->materials_progress }}%</span>
                                                    <div class="progress" style="width: 100px; height: 5px;">
                                                        <div class="progress-bar bg-gradient-info" role="progressbar" 
                                                             aria-valuenow="{{ $student->materials_progress }}" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100" 
                                                             style="width: {{ $student->materials_progress }}%">
                                                        </div>
                                                    </div>
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

            <!-- Recent Activity -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>Aktivitas Penyelesaian Terbaru</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="timeline timeline-one-side">
                                @foreach($recentProgress as $progress)
                                <div class="timeline-block mb-3">
                                    <span class="timeline-step">
                                        <i class="material-icons text-success">check_circle</i>
                                    </span>
                                    <div class="timeline-content">
                                        <h6 class="text-dark text-sm font-weight-bold mb-0">
                                        @if ($progress->user_id !=null)
                                                {{ $progress->user->name }} menyelesaikan soal
                                            
                                            @else
                                                'unknown'
                                            
                                            @endif
                                            <!-- {{ $progress->user->name }} menyelesaikan soal -->

                                        </h6>
                                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                            {{ $progress->material->title }}
                                        </p>
                                        <p class="text-sm mt-3 mb-0">
                                            {{ $progress->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-footers.auth></x-footers.auth>
        </div>
    </main>
    <x-plugins></x-plugins>
</x-layout>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
