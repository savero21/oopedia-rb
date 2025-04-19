<x-layout bodyClass="auth-layout bg-gray-200">
    <div>
        <div class="container position-sticky z-index-sticky top-0">
            <div class="row">
                <div class="col-12">
                    <!-- Navbar -->
                    <x-navbars.navs.guest signin='login' signup='register'></x-navbars.navs.guest>
                    <!-- End Navbar -->
                </div>
            </div>
        </div>
        <main class="main-content mt-0">
            <section>
                <div class="page-header min-vh-100">
                    <div class="container">
                        <div class="row">
                            <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 start-0 text-center justify-content-center flex-column">
                                <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center"
                                    style="background-image: url('{{ asset('images/background-log.jpg') }}'); background-size: cover; height: auto; min-height: 600px;">
                                    <div class="position-relative">
                                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-blue opacity-6 border-radius-lg"></div>
                                        <div class="position-relative z-index-1 p-4">
                                            <h2 class="text-white font-weight-bolder mb-4">Bergabunglah dengan OOPedia</h2>
                                            <p class="text-white opacity-8">Mengakses materi dengan lengkap dan terbaru dengan akun OOPedia</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column ms-auto me-auto ms-lg-auto me-lg-5">
                                <div class="card card-plain mt-8">
                                    <div class="card-header bg-transparent pb-0 text-center">
                                        <h4 class="font-weight-bolder text-primary">Daftar Akun</h4>
                                        <p class="mb-0">Masukkan data diri Anda untuk mendaftar</p>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" action="{{ route('register') }}">
                                            @csrf
                                            <label class="form-label">Nama</label>
                                            <div class="input-group input-group-outline mb-3">
                                                <input type="text" class="form-control" name="name"
                                                    value="{{ old('name') }}">
                                            </div>
                                            @error('name')
                                            <p class='text-danger inputerror'>{{ $message }} </p>
                                            @enderror
                                            <label class="form-label">Email</label>
                                            <div class="input-group input-group-outline mb-3">
                                                <input type="email" class="form-control" name="email"
                                                    value="{{ old('email') }}">
                                            </div>
                                            @error('email')
                                            <p class='text-danger inputerror'>{{ $message }} </p>
                                            @enderror
                                            <label class="form-label">Password</label>

                                            <div class="input-group input-group-outline mb-3">
                                                <input type="password" class="form-control" name="password">
                                            </div>
                                            @error('password')
                                            <p class='text-danger inputerror'>{{ $message }} </p>
                                            @enderror
                                            <label class="form-label">Konfirmasi Password</label>
                                            <div class="input-group input-group-outline mb-3">
                                                <input type="password" class="form-control" name="password_confirmation">
                                            </div>
                                            <div class="text-center">
                                                <button type="submit"
                                                    class="btn btn-lg bg-gradient-primary btn-lg w-100 mt-4 mb-0 register-btn">Daftar</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                        <p class="mb-2 text-sm mx-auto">
                                            Sudah memiliki akun?
                                            <a href="{{ route('login') }}"
                                                class="text-primary text-gradient font-weight-bold">Masuk</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <br><br>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    @push('js')
    <script src="{{ asset('assets') }}/js/jquery.min.js"></script>
    <script>
        $(function() {
            // Check if input has value on page load
            $(".input-group input").each(function() {
                if ($(this).val() !== "") {
                    $(this).parent().addClass('is-filled');
                }
            });
            
            // Check on input change
            $(".input-group input").on('focus blur input', function() {
                if ($(this).val() !== "") {
                    $(this).parent().addClass('is-filled');
                } else {
                    $(this).parent().removeClass('is-filled');
                }
            });
            
            // Add animation to register button
            $(".register-btn").hover(
                function() {
                    $(this).addClass("btn-pulse");
                },
                function() {
                    $(this).removeClass("btn-pulse");
                }
            );
        });
    </script>
    @endpush
    <link rel="stylesheet" href="{{ asset('css/blue-theme.css') }}">
</x-layout>