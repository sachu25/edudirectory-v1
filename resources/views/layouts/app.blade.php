<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon.png') }}">

        <title>{{ config('app.name', 'CIMS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- FontAwesome for icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --primary-color: #4F46E5;
                --primary-gradient: linear-gradient(135deg, #4F46E5 0%, #EC4899 100%);
                --secondary-color: #EC4899;
                --accent-gradient: linear-gradient(135deg, #EC4899 0%, #8B5CF6 100%);
                --bg-color: #F8FAFC;
                --card-bg: rgba(255, 255, 255, 0.9);
                --sidebar-width: 260px;
                --text-muted: #64748B;
                --border-color: rgba(226, 232, 240, 0.8);
            }
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                background-color: var(--bg-color);
                overflow-x: hidden;
                color: #1E293B;
            }
            .sidebar {
                width: var(--sidebar-width);
                height: 100vh;
                position: fixed;
                top: 0;
                left: 0;
                background: linear-gradient(180deg, #0F172A 0%, #1E1B4B 100%);
                color: white;
                z-index: 1000;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }
            .sidebar .nav-link {
                color: rgba(255, 255, 255, 0.65);
                padding: 12px 20px;
                border-radius: 8px;
                margin: 4px 16px;
                font-weight: 500;
                font-size: 0.9rem;
                display: flex;
                align-items: center;
                transition: all 0.2s ease-in-out;
            }
            .sidebar .nav-link i {
                font-size: 1.1rem;
                width: 28px;
                transition: transform 0.2s ease;
            }
            .sidebar .nav-link:hover {
                background-color: rgba(255, 255, 255, 0.08);
                color: white;
                transform: translateX(4px);
            }
            .sidebar .nav-link:hover i {
                transform: scale(1.1);
            }
            .sidebar .nav-link.active {
                background: var(--accent-gradient);
                color: white;
                box-shadow: 0 4px 15px rgba(236, 72, 153, 0.3);
                font-weight: 600;
            }
            .main-content {
                margin-left: var(--sidebar-width);
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                background-color: var(--bg-color);
                width: calc(100% - var(--sidebar-width));
            }
            .top-navbar {
                background-color: rgba(255, 255, 255, 0.75) !important;
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border-bottom: 1px solid var(--border-color);
                box-shadow: 0 1px 3px rgba(0,0,0,0.02);
                position: relative;
                z-index: 1010;
            }
            .btn-primary {
                background: var(--primary-gradient);
                border: none;
                box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
                transition: all 0.2s ease;
            }
            .btn-primary:hover {
                background: linear-gradient(135deg, #4338CA 0%, #6D28D9 100%);
                transform: translateY(-1px);
                box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3);
            }
            .btn-secondary {
                background-color: white;
                color: #4B5563;
                border: 1px solid #D1D5DB;
                transition: all 0.2s ease;
            }
            .btn-secondary:hover {
                background-color: #F9FAFB;
                color: #1F2937;
            }
            .text-primary {
                color: var(--primary-color) !important;
            }
            .card {
                border: 1px solid var(--border-color);
                background-color: var(--card-bg);
                backdrop-filter: blur(8px);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.025);
                border-radius: 12px;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }
            .card:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
            }
            .card-header {
                background-color: transparent;
                border-bottom: 1px solid var(--border-color);
            }
            .btn-outline-primary {
                color: var(--primary-color);
                border-color: var(--primary-color);
                transition: all 0.2s ease;
            }
            .btn-outline-primary:hover {
                background: var(--primary-gradient);
                border-color: transparent;
                color: white;
                box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
            }
            .badge.bg-primary {
                background: var(--primary-gradient) !important;
            }
            .badge.bg-secondary {
                background: var(--accent-gradient) !important;
            }

            /* Responsive Sidebar Styling */
            @media (max-width: 767.98px) {
                .sidebar {
                    left: calc(-1 * var(--sidebar-width));
                    position: fixed !important;
                    z-index: 1050;
                }
                .sidebar.show {
                    left: 0 !important;
                }
                .main-content {
                    margin-left: 0 !important;
                    width: 100% !important;
                }
                .dataTables_length {
                    margin-bottom: 12px !important;
                }
            }
        </style>
        
    </head>
    <body>
        <div class="d-flex">
            <!-- Sidebar -->
            <nav class="sidebar py-3">
                <div class="d-flex flex-column flex-grow-1" style="overflow-y: auto; width: 100%;">
                    <div class="text-center mb-4 px-3">
                        <img src="{{ asset('images/logo_sidebar.png') }}" alt="EduDirectory" style="width: 180px; height: auto;">
                    </div>
                    <ul class="nav flex-column mb-3">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('colleges.*') ? 'active' : '' }}" href="{{ route('colleges.index') }}">
                                <i class="fas fa-graduation-cap"></i> Institutions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}" href="{{ route('contacts.index') }}">
                                <i class="fas fa-address-book"></i> Contacts
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('interactions.*') ? 'active' : '' }}" href="{{ route('interactions.index') }}">
                                <i class="fas fa-handshake"></i> Interactions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                                <i class="fas fa-chart-bar"></i> Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('imports.*') || request()->routeIs('imports') ? 'active' : '' }}" href="{{ route('imports.index') }}">
                                <i class="fas fa-file-import"></i> Imports
                            </a>
                        </li>
                        <li class="nav-item mt-3 mb-1 px-3 text-uppercase" style="font-size: 0.75rem; font-weight: bold; letter-spacing: 0.05em; color: rgba(255, 255, 255, 0.45) !important;">
                            Settings & Masters
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('universities.*') ? 'active' : '' }}" href="{{ route('universities.index') }}">
                                <i class="fas fa-university"></i> Universities
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('designations.*') ? 'active' : '' }}" href="{{ route('designations.index') }}">
                                <i class="fas fa-id-badge"></i> Designations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('purposes.*') ? 'active' : '' }}" href="{{ route('purposes.index') }}">
                                <i class="fas fa-bullseye"></i> Purposes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('interaction-statuses.*') ? 'active' : '' }}" href="{{ route('interaction-statuses.index') }}">
                                <i class="fas fa-tasks"></i> Interaction Statuses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('contact-modes.*') ? 'active' : '' }}" href="{{ route('contact-modes.index') }}">
                                <i class="fas fa-comments"></i> Contact Modes
                            </a>
                        </li>
                        @if(auth()->user() && auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                <i class="fas fa-users"></i> Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                                <i class="fas fa-user-shield"></i> Roles & Permissions
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="main-content flex-grow-1">
                <!-- Top Navbar -->
                <nav class="navbar navbar-expand-lg top-navbar px-4 py-3">
                    <div class="container-fluid">
                        <button class="btn btn-outline-secondary d-md-none me-3" type="button" id="sidebarToggle">
                            <i class="fas fa-bars"></i>
                        </button>
                        
                        @isset($header)
                            <h4 class="mb-0 text-dark fw-semibold">{{ $header }}</h4>
                        @endisset

                        <div class="ms-auto d-flex align-items-center">
                            @auth
                            <div class="dropdown">
                                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-circle text-primary"></i> {{ Auth::user()->name }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Log Out</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            @endauth
                        </div>
                    </div>
                </nav>

                <!-- Page Content -->
                <main class="p-4 flex-grow-1">
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
                </main>
                
                <!-- Footer -->
                <footer class="bg-white text-center py-3 mt-auto border-top">
                    <p class="mb-0 text-muted">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                </footer>
            </div>
        </div>

        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <script>
            $(document).ready(function() {
                $('#sidebarToggle').on('click', function(e) {
                    e.preventDefault();
                    $('.sidebar').toggleClass('show');
                    
                    if ($('.sidebar').hasClass('show')) {
                        if ($('#sidebarBackdrop').length === 0) {
                            $('<div id="sidebarBackdrop" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.3); z-index: 1040;"></div>')
                                .appendTo('body')
                                .on('click', function() {
                                    $('.sidebar').removeClass('show');
                                    $(this).remove();
                                });
                        }
                    } else {
                        $('#sidebarBackdrop').remove();
                    }
                });
            });
        </script>
        
        @stack('scripts')
    </body>
</html>
