@props(['activePage', 'userName', 'userRole'])

<aside
    class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark"
    id="sidenav-main">
    <div class="sidenav-header d-flex flex-column align-items-center">
        <a class="navbar-brand m-0 d-flex text-wrap align-items-center" href="{{ route('dashboard') }}">
            <span class="font-weight-bold text-white">OOPEDIA</span>
        </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="d-flex align-items-center mx-3">
        <i class="material-icons opacity-10 me-2">person</i>
        <div class="flex-grow-1 text-center">
            <span class="font-weight-bold text-white">{{ $userName }}</span>
        </div>
        <span class="text-white ms-2">{{ $userRole }}</span>
    </div>
    <hr class="horizontal light mt-2 mb-2">
    <div class="collapse navbar-collapse w-auto max-height-vh-100" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'dashboard' ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('dashboard') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">dashboard</i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'materials' ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('materials.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">library_books</i>
                    </div>
                    <span class="nav-link-text ms-1">Materi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" 
                   data-bs-toggle="collapse" 
                   href="#questionsMenu" 
                   role="button" 
                   aria-expanded="false">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">quiz</i>
                    </div>
                    <span class="nav-link-text ms-1">Soal dan Jawaban</span>
                    <i class="material-icons ms-2">keyboard_arrow_down</i>
                </a>
                <div class="collapse" id="questionsMenu">
                    <ul class="nav nav-sm flex-column ms-4">
                        @foreach($materials as $material)
                            <li class="nav-item">
                                <a class="nav-link text-white" 
                                   href="#">
                                    <span class="sidenav-normal ms-2">{{ $material->title }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </li>
        </ul>
    </div>
    <div class="sidenav-footer position-absolute w-100 bottom-0 ">
        <div class="mx-3">
            <a class="btn bg-gradient-primary w-100" href="https://www.creative-tim.com/product/material-dashboard-laravel" target="_blank">Free Download</a>
        </div>
        <div class="mx-3">
            <a class="btn bg-gradient-primary w-100" href="../../documentation/getting-started/installation.html" target="_blank">View documentation</a>
        </div>
        <div class="mx-3">
            <a class="btn bg-gradient-primary w-100"
                href="https://www.creative-tim.com/product/material-dashboard-pro-laravel" target="_blank" type="button">Upgrade
                to pro</a>
        </div>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all dropdowns
    var dropdowns = document.querySelectorAll('[data-bs-toggle="collapse"]');
    dropdowns.forEach(function(dropdown) {
        dropdown.addEventListener('click', function(e) {
            e.preventDefault();
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.classList.toggle('show');
            }
        });
    });
});
</script>
