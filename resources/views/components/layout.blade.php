<!--
=========================================================
* Material Dashboard 2 - v3.0.0
=========================================================

* Product Page: https://www.creative-tim.com/product/material-dashboard
* Copyright 2021 Creative Tim (https://www.creative-tim.com) & UPDIVISION (https://www.updivision.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by www.creative-tim.com & www.updivision.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
@props(['bodyClass'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets') }}/img/apple-icon.png">
    <link rel="icon" type="image/png" href="{{ asset('assets') }}/img/favicon.png">
    <title>
        Media Pembalajaran OOPedia
    </title>
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <!-- Nucleo Icons -->
    <link href="{{ asset('assets') }}/css/nucleo-icons.css" rel="stylesheet" />
    <link href="{{ asset('assets') }}/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('assets') }}/css/material-dashboard.css?v=3.0.0" rel="stylesheet" />
    <script src="https://cdn.tiny.cloud/1/9iw2xqwn1593xsb15d6xpi0y41mtrets5ms0l5s8kekdgf63/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    @auth
    <style>
        /* Style khusus untuk user yang sudah login */
        .g-sidenav-show {
            overflow-x: hidden;
        }

        .g-sidenav-show .sidenav {
            z-index: 1009;
            position: fixed;
            display: block;
        }

        /* Perbaikan untuk main content */
        .main-content {
            position: relative;
            float: right;
            width: calc(100% - 280px);
            margin-left: 280px;
            min-height: 100vh;
            padding: 0;
            transition: all .2s ease-in-out;
        }

        .container-fluid {
            padding: 1.5rem 1.5rem !important;
            width: 100%;
            position: relative;
        }

        /* Responsive fixes */
        @media (max-width: 991.98px) {
            .main-content {
                width: 100%;
                margin-left: 0;
            }
            
            .g-sidenav-show.g-sidenav-pinned .main-content {
                margin-left: 280px;
            }
        }

        /* Fix untuk navbar */
        .navbar.navbar-main {
            margin-left: 0;
            margin-right: 0;
            left: 0;
            width: 100%;
        }

        /* Fix untuk cards */
        .card {
            margin-bottom: 1.5rem;
        }

        /* Fix untuk sidebar scroll */
        .sidenav {
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Fix untuk dropdown menu */
        #questionsMenu {
            position: relative;
            background: transparent;
        }

        #questionsMenu .nav-link {
            padding-left: 3rem;
        }
    </style>
    @endauth

    @guest
    <style>
        /* Style umum yang tetap berlaku untuk semua halaman */
        .g-sidenav-show {
            overflow-x: hidden;
        }

        /* Style khusus untuk halaman auth (sebelum login) */
        .auth-layout {
            min-height: 100vh;
            background-color: #f0f2f5;
        }

        .auth-layout .main-content {
            width: 100% !important;
            margin-left: 0 !important;
            min-height: 100vh;
            padding: 0;
        }

        .auth-layout .container-fluid {
            padding: 1.5rem 1.5rem !important;
            width: 100%;
            position: relative;
        }

        .auth-layout .card {
            margin-bottom: 1.5rem;
            max-width: 450px;
            margin: 0 auto;
        }

        /* Style untuk halaman setelah login */
        .dashboard-layout .g-sidenav-show .sidenav {
            z-index: 1009;
            position: fixed;
            display: block;
        }

        .dashboard-layout .main-content {
            position: relative;
            float: right;
            width: calc(100% - 280px);
            margin-left: 280px;
            min-height: 100vh;
            padding: 0;
            transition: all .2s ease-in-out;
        }

        .dashboard-layout .container-fluid {
            padding: 1.5rem 1.5rem !important;
            width: 100%;
            position: relative;
        }

        /* Responsive fixes untuk dashboard */
        @media (max-width: 991.98px) {
            .dashboard-layout .main-content {
                width: 100%;
                margin-left: 0;
            }
            
            .dashboard-layout.g-sidenav-show.g-sidenav-pinned .main-content {
                margin-left: 280px;
            }
        }

        /* Fix untuk navbar di dashboard */
        .dashboard-layout .navbar.navbar-main {
            margin-left: 0;
            margin-right: 0;
            left: 0;
            width: 100%;
        }

        /* Fix untuk sidebar scroll di dashboard */
        .dashboard-layout .sidenav {
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Fix untuk dropdown menu di dashboard */
        .dashboard-layout #questionsMenu {
            position: relative;
            background: transparent;
        }

        .dashboard-layout #questionsMenu .nav-link {
            padding-left: 3rem;
        }
    </style>
    @endguest

    <style>
        /* TinyMCE Editor Improvements */
        .tox-tinymce {
            min-height: 400px !important;
            margin-bottom: 20px;
        }

        /* Card Content Improvements */
        .card {
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Text Content Handling */
        .materi-description, 
        .question-content,
        .answer-content {
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: break-word;
            hyphens: auto;
            max-width: 100%;
        }

        /* TinyMCE Content Display */
        .content-display {
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: break-word;
            max-width: 100%;
            padding: 15px;
        }

        .content-display img {
            max-width: 100%;
            height: auto;
        }

        .content-display pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            background: #f5f5f5;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }

        .content-display code {
            background: #f5f5f5;
            padding: 2px 4px;
            border-radius: 4px;
        }

        /* Dashboard Card Improvements */
        .materi-card {
            height: 100%;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .materi-card:hover {
            transform: translateY(-5px);
        }

        .materi-card-body {
            padding: 1.5rem;
        }

        .materi-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #344767;
        }

        .materi-description {
            font-size: 0.875rem;
            color: #67748e;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
    @stack('head')
</head>
<body class="{{ $bodyClass }}">

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{ $slot }}

<script src="{{ asset('assets') }}/js/core/popper.min.js"></script>
<script src="{{ asset('assets') }}/js/core/bootstrap.min.js"></script>
<script src="{{ asset('assets') }}/js/plugins/perfect-scrollbar.min.js"></script>
<script src="{{ asset('assets') }}/js/plugins/smooth-scrollbar.min.js"></script>
@stack('js')
<script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Perfect Scrollbar initialization
        if (document.querySelector('.sidenav')) {
            var fixedPlugin = document.querySelector('.fixed-plugin');
            var fixedPluginButton = document.querySelector('.fixed-plugin-button');
            var fixedPluginButtonNav = document.querySelector('.fixed-plugin-button-nav');
            var fixedPluginCard = document.querySelector('.fixed-plugin .card');
            var fixedPluginCloseButton = document.querySelectorAll('.fixed-plugin-close-button');
            var navbar = document.getElementById('navbarBlur');
            var buttonNavbarFixed = document.getElementById('navbarFixed');

            if (fixedPluginButton) {
                fixedPluginButton.onclick = function() {
                    if (!fixedPlugin.classList.contains('show')) {
                        fixedPlugin.classList.add('show');
                    } else {
                        fixedPlugin.classList.remove('show');
                    }
                }
            }

            document.querySelector('body').onclick = function(e) {
                if (e.target != fixedPluginButton && e.target != fixedPluginButtonNav && e.target.closest('.fixed-plugin .card') != fixedPluginCard) {
                    fixedPlugin.classList.remove('show');
                }
            }
        }
    });

    // Add this before the existing script content
    tinymce.init({
        selector: 'textarea.tinymce',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        menubar: false,
        height: 400,
        content_style: `
            body { 
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
                font-size: 16px;
                line-height: 1.6;
                color: #333;
                margin: 15px;
            }
            p { margin: 0 0 1em 0; }
            img { max-width: 100%; height: auto; }
            pre { background: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto; }
            code { background: #f5f5f5; padding: 2px 4px; border-radius: 4px; }
        `,
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        }
    });
</script>
<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
<script src="{{ asset('assets') }}/js/material-dashboard.min.js?v=3.0.0"></script>
</body>
</html>
