<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'e-Rapor') — {{ config('app.name', 'e-Rapor') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700" rel="stylesheet">

    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #1e1e2f;
            --sidebar-hover: #2a2a3d;
            --sidebar-active: #33334d;
            --sidebar-text: #a0a0b8;
            --sidebar-text-active: #ffffff;
            --sidebar-accent: #4e73df;
            --body-bg: #f8f9fa;
        }

        body {
            background-color: var(--body-bg);
            font-family: 'Nunito', sans-serif;
            overflow-x: hidden;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            transition: transform .25s ease;
        }

        .sidebar-brand {
            height: 60px;
            display: flex;
            align-items: center;
            padding: 0 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .sidebar-brand h5 {
            color: #fff;
            margin: 0;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: .5px;
        }

        .sidebar-nav {
            padding: .75rem 0;
            list-style: none;
            margin: 0;
        }

        .sidebar-heading {
            padding: .75rem 1.25rem .35rem;
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,.25);
            font-weight: 700;
        }

        .sidebar-item {}

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: .55rem 1.25rem;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: .85rem;
            font-weight: 600;
            transition: all .15s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-link:hover {
            color: var(--sidebar-text-active);
            background: var(--sidebar-hover);
        }

        .sidebar-link.active {
            color: var(--sidebar-text-active);
            background: var(--sidebar-active);
            border-left-color: var(--sidebar-accent);
        }

        .sidebar-link i {
            width: 20px;
            text-align: center;
            margin-right: .75rem;
            font-size: .8rem;
        }

        .sidebar-submenu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-submenu .sidebar-link {
            padding-left: 3rem;
            font-size: .82rem;
            font-weight: 400;
        }

        /* ── Topbar ── */
        .topbar {
            height: 60px;
            background: #fff;
            border-bottom: 1px solid #e3e6f0;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
        }

        .topbar .btn-toggle {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: #5a5c69;
            cursor: pointer;
            padding: .25rem .5rem;
        }

        .topbar .btn-toggle:hover {
            color: var(--sidebar-accent);
        }

        /* ── Main content wrapper ── */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            transition: margin-left .25s ease;
        }

        .content-area {
            padding: 1.5rem;
        }

        /* ── Cards ── */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 .125rem .25rem rgba(0,0,0,.06);
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            font-weight: 700;
            font-size: .9rem;
            padding: .85rem 1.25rem;
            border-radius: 10px 10px 0 0 !important;
        }

        /* ── Tables ── */
        .table thead th {
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #6c757d;
            font-weight: 700;
            border-bottom: 2px solid #dee2e6;
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
            font-size: .85rem;
        }

        /* ── Badge / stat card ── */
        .stat-card {
            border-radius: 10px;
            padding: 1.25rem;
            background: #fff;
            box-shadow: 0 .125rem .25rem rgba(0,0,0,.06);
        }

        .stat-card .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #212529;
        }

        .stat-card .stat-label {
            font-size: .78rem;
            color: #6c757d;
            font-weight: 600;
        }

        /* ── Backdrop ── */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.35);
            z-index: 1035;
        }

        /* ── Responsive ── */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar.show ~ .sidebar-backdrop {
                display: block;
            }

            .main-wrapper {
                margin-left: 0;
            }
        }

        /* ── Misc ── */
        .page-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 0;
        }

        .breadcrumb {
            font-size: .8rem;
            margin-bottom: 0;
            background: none;
            padding: 0;
        }
    </style>

    @stack('styles')
</head>
<body>
    @auth
        {{-- Sidebar --}}
        @include('partials.sidebar')
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        {{-- Main --}}
        <div class="main-wrapper">
            {{-- Topbar --}}
            <div class="topbar">
                <button class="btn-toggle me-3 d-lg-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <button class="btn-toggle me-3 d-none d-lg-inline-block" id="sidebarToggleDesktop">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="ms-auto d-flex align-items-center">
                    <div class="dropdown">
                        <a class="text-decoration-none text-dark d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <span class="d-none d-sm-inline me-2" style="font-size:.85rem;font-weight:600;">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down" style="font-size:.65rem;color:#6c757d;"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size:.85rem;">
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2 text-muted"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                </div>
            </div>

            {{-- Content --}}
            <div class="content-area">
                {{-- Flash messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show py-2 px-3" style="font-size:.85rem;" role="alert">
                        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show py-2 px-3" style="font-size:.85rem;" role="alert">
                        <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Validation errors --}}
                @if($errors->any())
                    <div class="alert alert-danger py-2 px-3" style="font-size:.85rem;">
                        <i class="fas fa-exclamation-triangle me-1"></i> <strong>Terdapat kesalahan:</strong>
                        <ul class="mb-0 mt-1 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    @else
        {{-- Guest layout (login/register) --}}
        <nav class="navbar navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                    <i class="fas fa-graduation-cap me-2" style="color:var(--sidebar-accent,#4e73df);"></i>e-Rapor
                </a>
            </div>
        </nav>
        <main class="py-4">
            @yield('content')
        </main>
    @endauth

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const toggleMobile = document.getElementById('sidebarToggle');
            const toggleDesktop = document.getElementById('sidebarToggleDesktop');

            if (toggleMobile) {
                toggleMobile.addEventListener('click', function () {
                    sidebar.classList.toggle('show');
                });
            }

            if (backdrop) {
                backdrop.addEventListener('click', function () {
                    sidebar.classList.remove('show');
                });
            }

            if (toggleDesktop) {
                toggleDesktop.addEventListener('click', function () {
                    const wrapper = document.querySelector('.main-wrapper');
                    if (sidebar.style.transform === 'translateX(-100%)') {
                        sidebar.style.transform = '';
                        wrapper.style.marginLeft = '';
                    } else {
                        sidebar.style.transform = 'translateX(-100%)';
                        wrapper.style.marginLeft = '0';
                    }
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
