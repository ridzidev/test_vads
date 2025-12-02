<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Real-Time Web Chat Application')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar-active {
            @apply bg-blue-600 text-white;
        }
        .transition-all {
            transition: all 0.3s ease;
        }
        /* Style untuk membuat sidebar scrollable dan tombol logout fixed di bawah */
        #sidebar {
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Menarik konten ke atas dan logout ke bawah */
        }
        #sidebar-content {
            flex-grow: 1;
            overflow-y: auto;
        }
        #sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid theme('colors.blue.800');
            background-color: theme('colors.gray.900'); /* Latar belakang sama dengan sidebar */
            z-index: 50; /* Pastikan di atas konten sidebar */
        }
    </style>
    @yield('extra-css')
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <div class="w-64 bg-gradient-to-br from-gray-900 to-blue-900 text-white fixed h-full transition-all duration-300 ease-in-out z-40 hidden lg:flex flex-col shadow-xl" id="sidebar">
            
            <div class="p-6 border-b border-blue-800 flex items-center gap-3">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'VADS') }}&background=0D8ABC&color=fff" class="w-10 h-10 rounded-full border-2 border-blue-500" alt="Avatar">
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <i class="fas fa-cube"></i> VADS
                </h1>
            </div>

            <div id="sidebar-content" class="flex-grow">
                <nav class="mt-6">
                    @if(Auth::check())
                        <div class="px-6 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-user-circle text-lg text-blue-400"></i>
                            <span class="truncate">{{ Auth::user()->name ?? Auth::user()->email }}</span>
                            <span class="ml-auto px-2 py-1 rounded bg-blue-700 text-white text-xs">{{ Auth::user()->role === 'admin' ? 'Admin' : 'Customer' }}</span>
                        </div>

                        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'admins')
                            <a href="/admin-dashboard" class="nav-link px-6 py-3 block rounded-lg my-1 hover:bg-blue-800 transition-all duration-200 ease-in-out flex items-center gap-3 @if(Route::current()->getName() === 'admin-dashboard') sidebar-active @endif">
                                <i class="fas fa-chart-line"></i> <span>Dashboard Admin</span>
                            </a>
                            <a href="/master-customer" class="nav-link px-6 py-3 block rounded-lg my-1 hover:bg-blue-800 transition-all duration-200 ease-in-out flex items-center gap-3 @if(Route::current()->getName() === 'master-customer') sidebar-active @endif">
                                <i class="fas fa-users"></i> <span>Master Customer</span>
                            </a>
                            <a href="/service-desk-dashboard" class="nav-link px-6 py-3 block rounded-lg my-1 hover:bg-blue-800 transition-all duration-200 ease-in-out flex items-center gap-3 @if(Route::current()->getName() === 'service-desk-dashboard') sidebar-active @endif">
                                <i class="fas fa-headset"></i> <span>Service Desk</span>
                            </a>
                        @else
                            <a href="/dashboard" class="nav-link px-6 py-3 block rounded-lg my-1 hover:bg-blue-800 transition-all duration-200 ease-in-out flex items-center gap-3 @if(Route::current()->getName() === 'dashboard') sidebar-active @endif">
                                <i class="fas fa-home"></i> <span>Dashboard</span>
                            </a>

                            <a href="/chat-room" class="nav-link px-6 py-3 block rounded-lg my-1 hover:bg-blue-800 transition-all duration-200 ease-in-out flex items-center gap-3 @if(Route::current()->getName() === 'chat-room') sidebar-active @endif">
                                <i class="fas fa-comments"></i> <span>Chat</span>
                            </a>
                        @endif
                    @endif
                </nav>
            </div>

            @if(Auth::check())
            <div id="sidebar-footer">
                <form action="/logout" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="w-full text-left py-3 px-6 rounded-lg bg-red-600 hover:bg-red-700 text-white transition-all duration-200 ease-in-out flex items-center justify-center gap-3 font-semibold shadow-md hover:shadow-lg">
                        <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                    </button>
                </form>
            </div>
            @endif
        </div>

        <div class="flex-1 ml-0 lg:ml-64 flex flex-col">
            <nav class="bg-white shadow-lg px-6 py-4 flex justify-between items-center sticky top-0 z-30">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="text-gray-700 hover:text-blue-700 transition focus:outline-none lg:hidden">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                    <h2 class="text-2xl font-bold text-blue-800 tracking-tight">@yield('page-title', 'VADS')</h2>
                </div>

                <div class="flex items-center gap-6">
                    <div class="relative hidden sm:block">
                        <input type="text" placeholder="Search..." class="px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm w-40">
                        <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
                    </div>
                    <button class="relative text-gray-700 hover:text-blue-700 transition">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-1">3</span>
                    </button>
                    @if(Auth::check())
                        <div class="relative" id="userDropdown">
                            <button class="flex items-center gap-2 focus:outline-none" onclick="toggleUserDropdown(event)">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'U') }}&background=0D8ABC&color=fff" class="w-8 h-8 rounded-full border-2 border-blue-500" alt="Avatar">
                                <span class="font-semibold text-gray-800 hidden sm:inline">{{ Auth::user()->name ?? Auth::user()->email }}</span>
                                <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-2 hidden z-50" id="userDropdownMenu">
                                <div class="px-4 py-2 text-sm text-gray-700 border-b">Role: <span class="font-bold text-blue-600">{{ Auth::user()->role }}</span></div>
                                <a href="#" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 transition flex items-center gap-2">
                                    <i class="fas fa-user-cog"></i> Pengaturan Profil
                                </a>
                            </div>
                        </div>
                    @else
                        <a href="/login" class="text-blue-600 hover:text-blue-800 transition flex items-center gap-2">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    @endif
                </div>
            </nav>

            <div class="flex-1 overflow-auto p-6 bg-gradient-to-br from-gray-50 to-blue-50">
                @if($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-600 text-red-700 p-4 mb-4 rounded">
                        <p class="font-bold flex items-center gap-2">
                            <i class="fas fa-exclamation-circle"></i> Terjadi Kesalahan
                        </p>
                        <ul class="mt-2 ml-6">
                            @foreach($errors->all() as $error)
                                <li class="list-disc">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-600 text-green-700 p-4 mb-4 rounded flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                        <button onclick="this.parentElement.style.display='none'" class="text-green-700 hover:text-green-900">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-600 text-red-700 p-4 mb-4 rounded flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-times-circle"></i> {{ session('error') }}
                        </div>
                        <button onclick="this.parentElement.style.display='none'" class="text-red-700 hover:text-red-900">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @yield('content')
            </div>

            <footer class="bg-gray-200 text-gray-700 p-4 text-center text-sm shadow-inner">
                <p>&copy; 2025 Real-Time Web Chat Application. All rights reserved. Created by ridzidev.</p>
            </footer>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            // Untuk layar kecil, toggle class hidden dan flex
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('flex'); 
            
            // Atur overflow body agar konten utama tidak bergerak saat sidebar terbuka
            if (!sidebar.classList.contains('hidden')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        // Dropdown for user menu (click toggle)
        function toggleUserDropdown(event) {
            event.stopPropagation();
            const menu = document.getElementById('userDropdownMenu');
            menu.classList.toggle('hidden');
        }
        document.addEventListener('click', function(e) {
            const menu = document.getElementById('userDropdownMenu');
            const dropdown = document.getElementById('userDropdown');
            if (menu && dropdown && !dropdown.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('[class*="bg-green"], [class*="bg-red"]');
            alerts.forEach(alert => {
                // Hanya atur timer jika alert memiliki tombol close
                if (alert.querySelector('button[onclick*="display=\'none\'"]')) {
                    setTimeout(() => {
                        alert.style.transition = 'opacity 0.5s ease';
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 500);
                    }, 5000); // 5 detik
                }
            });
        }, 0);
    </script>
    @yield('extra-js')
</body>
</html>