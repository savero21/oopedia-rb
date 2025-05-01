@extends('mahasiswa.layouts.app')

@section('title', 'UEQ Survey')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>User Experience Questionnaire (UEQ)</h4>
                </div>
                <div class="card-body">
                    <p class="mb-4">Silakan berikan penilaian Anda terhadap aplikasi pembelajaran OOPEDIA dengan memilih nilai pada skala berikut:</p>
                    
                    <form method="POST" action="{{ route('mahasiswa.ueq.store') }}">
                        @csrf
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th width="30%">Aspek</th>
                                        <th colspan="7" class="text-center">Penilaian</th>
                                        <th width="30%">Aspek</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- UEQ Items -->
                                    <tr>
                                        <td>Menyebalkan</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="annoying_enjoyable" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Menyenangkan</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Tidak dapat dipahami</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="not_understandable_understandable" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Dapat dipahami</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Kreatif</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="creative_dull" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Monoton</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Mudah</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="easy_difficult" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Sulit</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Bermanfaat</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="valuable_inferior" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Kurang bermanfaat</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Membosankan</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="boring_exciting" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Menarik</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Tidak menarik</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="not_interesting_interesting" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Menarik</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Tidak dapat diprediksi</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="unpredictable_predictable" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Dapat diprediksi</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Cepat</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="fast_slow" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Lambat</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Berdaya cipta</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="inventive_conventional" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Konvensional</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Menghalangi</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="obstructive_supportive" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Mendukung</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Baik</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="good_bad" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Buruk</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Rumit</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="complicated_easy" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Sederhana</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Tidak disukai</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="unlikable_pleasing" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Menyenangkan</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Biasa</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="usual_leading_edge" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Terdepan</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Tidak menyenangkan</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="unpleasant_pleasant" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Menyenangkan</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Aman</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="secure_not_secure" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Tidak aman</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Memotivasi</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="motivating_demotivating" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Tidak memotivasi</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Memenuhi ekspektasi</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="meets_expectations_does_not_meet" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Tidak memenuhi ekspektasi</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Tidak efisien</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="inefficient_efficient" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Efisien</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Jelas</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="clear_confusing" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Membingungkan</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Tidak praktis</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="impractical_practical" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Praktis</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Terorganisir</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="organized_cluttered" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Berantakan</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Menarik</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="attractive_unattractive" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Tidak menarik</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Ramah</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="friendly_unfriendly" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Tidak ramah</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Konservatif</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center">
                                                <input type="radio" name="conservative_innovative" value="{{ $i }}" required>
                                                <label>{{ $i }}</label>
                                            </td>
                                        @endfor
                                        <td>Inovatif</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Submit Survey
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 