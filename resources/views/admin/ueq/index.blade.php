<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="ueq" :userName="$userName" :userRole="$userRole" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="UEQ Survey Results" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <br><br>
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3">Hasil Survey UEQ</h6>
                                <a href="{{ route('admin.ueq.export') }}" class="btn btn-sm btn-success me-3">
                                    <i class="material-icons text-sm">download</i> Export CSV
                                </a>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            @if($surveys->isEmpty())
                                <div class="text-center p-4">
                                    <p class="mb-0">Belum ada data survey UEQ yang tersedia.</p>
                                </div>
                            @else
                                <!-- UEQ Averages Summary -->
                                <div class="row mx-3 mb-4">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header p-3">
                                                <h6 class="mb-0">UEQ Dimensions Average Scores</h6>
                                            </div>
                                            <div class="card-body p-3">
                                                <div class="row">
                                                    @foreach($averages as $dimension => $score)
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card shadow-sm">
                                                                <div class="card-body p-3">
                                                                    <h6 class="text-capitalize">{{ $dimension }}</h6>
                                                                    <div class="progress-container">
                                                                        <div class="progress">
                                                                            <div class="progress-bar bg-gradient-info" role="progressbar" 
                                                                                style="width: {{ min(max(($score/7)*100, 0), 100) }}%" 
                                                                                aria-valuenow="{{ $score }}" aria-valuemin="1" aria-valuemax="7">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <p class="mt-2 mb-0 text-sm">
                                                                        <span class="font-weight-bold">Score: {{ number_format($score, 2) }}/7</span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- UEQ Survey Responses Table -->
                                <div class="table-responsive p-0">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">User</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Attractiveness</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Perspicuity</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Efficiency</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Dependability</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Stimulation</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Novelty</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($surveys as $survey)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex px-2 py-1">
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <h6 class="mb-0 text-sm">{{ $survey->user->name }}</h6>
                                                                <p class="text-xs text-secondary mb-0">{{ $survey->user->email }}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0">{{ $survey->created_at->format('d M Y') }}</p>
                                                        <p class="text-xs text-secondary mb-0">{{ $survey->created_at->format('H:i') }}</p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0">
                                                            {{ number_format(($survey->annoying_enjoyable + $survey->good_bad + $survey->unlikable_pleasing + $survey->unpleasant_pleasant + $survey->attractive_unattractive + $survey->friendly_unfriendly) / 6, 2) }}
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0">
                                                            {{ number_format(($survey->not_understandable_understandable + $survey->easy_difficult + $survey->complicated_easy + $survey->clear_confusing) / 4, 2) }}
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0">
                                                            {{ number_format(($survey->fast_slow + $survey->inefficient_efficient + $survey->impractical_practical + $survey->organized_cluttered) / 4, 2) }}
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0">
                                                            {{ number_format(($survey->unpredictable_predictable + $survey->obstructive_supportive + $survey->secure_not_secure + $survey->meets_expectations_does_not_meet) / 4, 2) }}
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0">
                                                            {{ number_format(($survey->valuable_inferior + $survey->boring_exciting + $survey->not_interesting_interesting + $survey->motivating_demotivating) / 4, 2) }}
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0">
                                                            {{ number_format(($survey->creative_dull + $survey->inventive_conventional + $survey->usual_leading_edge + $survey->conservative_innovative) / 4, 2) }}
                                                        </p>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-layout> 