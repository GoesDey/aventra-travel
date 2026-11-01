<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'TriHitaTour' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @livewireStyles
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-display-lg, .font-headline-md, .font-price-hero { font-family: 'Montserrat', sans-serif; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .text-shadow-subtle {
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body class="bg-background text-on-surface antialiased flex flex-col min-h-screen">
    <!-- TopNavBar -->
    <nav class="fixed top-0 w-full z-50 bg-white/90 backdrop-blur-md border-b border-outline-variant/30 shadow-sm transition-all duration-300" id="navbar">
        <div class="flex justify-between items-center px-margin-mobile md:px-margin-desktop py-3.5 max-w-container-max mx-auto">
            <!-- Brand -->
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="font-display-lg text-xl md:text-2xl font-bold text-oceanic-deep tracking-tight hover:opacity-85 transition-opacity flex items-center gap-2">
                    <span class="material-symbols-outlined text-tropical-teal fill text-3xl">travel_explore</span>
                    <span>TriHitaTour</span>
                </a>
            </div>

            <!-- Desktop Navigation Links (Role-Based Minimalist) -->
            <div class="hidden md:flex items-center space-x-8 text-sm font-medium">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-tropical-teal font-semibold' : 'text-on-surface-variant hover:text-tropical-teal transition-colors' }}">
                    Home
                </a>

                @guest
                    <a href="{{ route('packages.index') }}" class="{{ request()->routeIs('packages.*') ? 'text-tropical-teal font-semibold' : 'text-on-surface-variant hover:text-tropical-teal transition-colors' }}">
                        Packages
                    </a>
                @endguest

                @auth
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('super-admin.dashboard') }}" class="{{ request()->routeIs('super-admin.*') ? 'text-tropical-teal font-semibold' : 'text-on-surface-variant hover:text-tropical-teal transition-colors' }}">
                            Dashboard Admin
                        </a>
                    @elseif(auth()->user()->isAgent())
                        <a href="{{ route('agent.dashboard') }}" class="{{ request()->routeIs('agent.*') ? 'text-tropical-teal font-semibold' : 'text-on-surface-variant hover:text-tropical-teal transition-colors' }}">
                            Dashboard Agen
                        </a>
                    @else
                        <a href="{{ route('packages.index') }}" class="{{ request()->routeIs('packages.*') ? 'text-tropical-teal font-semibold' : 'text-on-surface-variant hover:text-tropical-teal transition-colors' }}">
                            Packages
                        </a>
                        <a href="{{ route('history') }}" class="{{ request()->routeIs('history') ? 'text-tropical-teal font-semibold' : 'text-on-surface-variant hover:text-tropical-teal transition-colors' }}">
                            History
                        </a>
                    @endif
                @endauth
            </div>

            <!-- Right Side: Guest Actions or Auth Profile Dropdown -->
            <div class="hidden md:flex items-center space-x-4">
                @guest
                    <a href="{{ route('login') }}" class="text-on-surface-variant hover:text-tropical-teal font-semibold text-sm px-3 py-2 transition-colors">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="bg-tropical-teal hover:bg-secondary text-white font-semibold text-sm px-5 py-2.5 rounded-full transition-all shadow-sm hover:shadow active:scale-95">
                        Register
                    </a>
                @else
                    <!-- Profile Dropdown (Alpine.js) -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                type="button" 
                                class="flex items-center gap-2.5 py-1.5 px-3 rounded-full hover:bg-surface-container-low transition-colors border border-outline-variant/30 focus:outline-none focus:ring-2 focus:ring-tropical-teal/30 cursor-pointer">
                            <div class="w-8 h-8 rounded-full bg-oceanic-deep text-white flex items-center justify-center font-bold text-xs shadow-inner">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="text-sm font-semibold text-on-surface max-w-[140px] truncate">
                                {{ auth()->user()->name }}
                            </span>
                            <span class="material-symbols-outlined text-outline text-lg transition-transform duration-200" :class="{ 'rotate-180': open }">
                                expand_more
                            </span>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                             style="display: none;"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-outline-variant/30 py-2 z-50">
                            
                            <!-- User Info in Dropdown -->
                            <div class="px-4 py-2.5 border-b border-outline-variant/20">
                                <p class="text-xs text-outline font-medium">Masuk sebagai</p>
                                <p class="text-sm font-bold text-on-surface truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-on-surface-variant truncate">{{ auth()->user()->email }}</p>
                                <div class="mt-1.5">
                                    @if(auth()->user()->isSuperAdmin())
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-100 text-purple-700">Super Admin</span>
                                    @elseif(auth()->user()->isAgent())
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-700">Mitra Agen</span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold bg-sky-100 text-sky-700">Wisatawan</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Role-Specific Dropdown Navigation -->
                            <div class="py-1">
                                @if(auth()->user()->isSuperAdmin())
                                    <a href="{{ route('super-admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-on-surface hover:bg-surface-container-low transition-colors">
                                        <span class="material-symbols-outlined text-base text-purple-600">admin_panel_settings</span>
                                        <span>Dashboard Admin</span>
                                    </a>
                                    <a href="{{ route('admin.bookings') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-on-surface hover:bg-surface-container-low transition-colors">
                                        <span class="material-symbols-outlined text-base text-oceanic-deep">receipt_long</span>
                                        <span>Manajemen Booking</span>
                                    </a>
                                @elseif(auth()->user()->isAgent())
                                    <a href="{{ route('agent.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-on-surface hover:bg-surface-container-low transition-colors">
                                        <span class="material-symbols-outlined text-base text-tropical-teal">dashboard</span>
                                        <span>Dashboard Agen</span>
                                    </a>
                                    <a href="{{ route('agent.packages.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-on-surface hover:bg-surface-container-low transition-colors">
                                        <span class="material-symbols-outlined text-base text-tropical-teal">tour</span>
                                        <span>Kelola Paket</span>
                                    </a>
                                @else
                                    <a href="{{ route('packages.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-on-surface hover:bg-surface-container-low transition-colors">
                                        <span class="material-symbols-outlined text-base text-tropical-teal">travel_explore</span>
                                        <span>Katalog Paket</span>
                                    </a>
                                    <a href="{{ route('history') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-on-surface hover:bg-surface-container-low transition-colors">
                                        <span class="material-symbols-outlined text-base text-tropical-teal">receipt_long</span>
                                        <span>Riwayat Pesanan</span>
                                    </a>
                                @endif
                            </div>

                            <!-- Logout -->
                            <div class="border-t border-outline-variant/20 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-error hover:bg-error-container/30 transition-colors text-left cursor-pointer">
                                        <span class="material-symbols-outlined text-base">logout</span>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endguest
            </div>

            <!-- Mobile Menu Toggle Button -->
            <button id="mobileMenuButton" type="button" class="md:hidden text-oceanic-deep p-2 focus:outline-none">
                <span class="material-symbols-outlined text-2xl">menu</span>
            </button>
        </div>

        <!-- Mobile Drawer Navigation -->
        <div id="mobileMenu" class="hidden md:hidden border-t border-outline-variant/30 bg-white/95 backdrop-blur-xl px-6 py-5 space-y-4 shadow-xl">
            <div class="space-y-1">
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('home') ? 'bg-tropical-teal/10 text-tropical-teal' : 'text-on-surface hover:bg-surface-container-low' }}">
                    Home
                </a>

                @guest
                    <a href="{{ route('packages.index') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('packages.*') ? 'bg-tropical-teal/10 text-tropical-teal' : 'text-on-surface hover:bg-surface-container-low' }}">
                        Packages
                    </a>
                @endguest

                @auth
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('super-admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('super-admin.*') ? 'bg-tropical-teal/10 text-tropical-teal' : 'text-on-surface hover:bg-surface-container-low' }}">
                            Dashboard Admin
                        </a>
                    @elseif(auth()->user()->isAgent())
                        <a href="{{ route('agent.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('agent.*') ? 'bg-tropical-teal/10 text-tropical-teal' : 'text-on-surface hover:bg-surface-container-low' }}">
                            Dashboard Agen
                        </a>
                    @else
                        <a href="{{ route('packages.index') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('packages.*') ? 'bg-tropical-teal/10 text-tropical-teal' : 'text-on-surface hover:bg-surface-container-low' }}">
                            Packages
                        </a>
                        <a href="{{ route('history') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('history') ? 'bg-tropical-teal/10 text-tropical-teal' : 'text-on-surface hover:bg-surface-container-low' }}">
                            History
                        </a>
                    @endif
                @endauth
            </div>

            @auth
                <div class="pt-3 border-t border-outline-variant/30 space-y-3">
                    <div class="flex items-center justify-between px-3 py-1">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-oceanic-deep text-white flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="text-xs font-bold text-on-surface">{{ auth()->user()->name }}</span>
                        </div>
                        @if(auth()->user()->isSuperAdmin())
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-purple-100 text-purple-800">Super Admin</span>
                        @elseif(auth()->user()->isAgent())
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-800">Mitra Agen</span>
                        @else
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-sky-100 text-sky-800">Wisatawan</span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-error bg-error-container/20 font-bold text-sm">
                            <span class="material-symbols-outlined text-sm">logout</span>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            @else
                <div class="pt-3 border-t border-outline-variant/30 flex flex-col gap-2">
                    <a href="{{ route('login') }}" class="text-center py-2.5 rounded-xl text-oceanic-deep font-bold text-sm border border-outline-variant/50">Login</a>
                    <a href="{{ route('register') }}" class="text-center py-2.5 rounded-xl bg-tropical-teal text-white font-bold text-sm shadow">Register</a>
                </div>
            @endauth
        </div>
    </nav>

    <main class="flex-grow {{ request()->routeIs('home') ? '' : 'pt-24' }}">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="w-full bg-oceanic-deep dark:bg-on-background text-surface-white mt-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-gutter px-margin-mobile md:px-margin-desktop py-16 max-w-container-max mx-auto">
            <!-- Brand Column -->
            <div class="flex flex-col space-y-6">
                <a class="font-display-lg-mobile md:font-display-lg text-display-lg-mobile md:text-display-lg font-bold text-surface-white flex items-center space-x-2" href="{{ route('home') }}">
                    <span class="material-symbols-outlined text-tropical-teal text-4xl">travel_explore</span>
                    <span class="">TriHitaTour</span>
                </a>
                <p class="font-body-md text-body-md text-surface-variant">Solusi perjalanan modern yang menggabungkan kemewahan, kenyamanan, dan keamanan untuk pengalaman tak terlupakan.</p>
                <div class="flex space-x-4">
                    <a class="w-10 h-10 rounded-full bg-surface-white/10 flex items-center justify-center hover:bg-tropical-teal transition-colors" href="#">
                        <span class="material-symbols-outlined text-sm">public</span>
                    </a>
                    <a class="w-10 h-10 rounded-full bg-surface-white/10 flex items-center justify-center hover:bg-tropical-teal transition-colors" href="#">
                        <span class="material-symbols-outlined text-sm">chat</span>
                    </a>
                    <a class="w-10 h-10 rounded-full bg-surface-white/10 flex items-center justify-center hover:bg-tropical-teal transition-colors" href="#">
                        <span class="material-symbols-outlined text-sm">alternate_email</span>
                    </a>
                </div>
            </div>
            <!-- Company Links -->
            <div class="flex flex-col space-y-4">
                <h4 class="font-label-bold text-label-bold uppercase tracking-wider text-surface-variant mb-2">PERUSAHAAN</h4>
                <a class="font-body-md text-body-md text-surface-variant hover:text-soft-coral transition-colors" href="#">Company</a>
                <a class="font-body-md text-body-md text-surface-variant hover:text-soft-coral transition-colors" href="#">Karir</a>
                <a class="font-body-md text-body-md text-surface-variant hover:text-soft-coral transition-colors" href="#">Blog Travel</a>
                <a class="font-body-md text-body-md text-surface-variant hover:text-soft-coral transition-colors" href="#">Kerjasama</a>
            </div>
            <!-- Help Links -->
            <div class="flex flex-col space-y-4">
                <h4 class="font-label-bold text-label-bold uppercase tracking-wider text-surface-variant mb-2">BANTUAN</h4>
                <a class="font-body-md text-body-md text-surface-variant hover:text-soft-coral transition-colors" href="#">Help Center</a>
                <a class="font-body-md text-body-md text-surface-variant hover:text-soft-coral transition-colors" href="#">Terms of Service</a>
                <a class="font-body-md text-body-md text-surface-variant hover:text-soft-coral transition-colors" href="#">Privacy Policy</a>
                <a class="font-body-md text-body-md text-surface-variant hover:text-soft-coral transition-colors" href="#">Refund &amp; Pembatalan</a>
            </div>
            <!-- Newsletter -->
            <div class="flex flex-col space-y-4">
                <h4 class="font-label-bold text-label-bold uppercase tracking-wider text-surface-variant mb-2">BERLANGGANAN</h4>
                <p class="font-body-md text-body-md text-surface-variant">Dapatkan info promo terbaru langsung di inbox Anda.</p>
                <div class="flex mt-2">
                    <input class="bg-surface-white/10 border-none rounded-l-md px-4 py-2 text-surface-white placeholder-surface-variant/50 focus:ring-1 focus:ring-tropical-teal w-full font-body-md text-body-md" placeholder="Email" type="email">
                    <button class="bg-tropical-teal hover:bg-secondary text-surface-white px-4 py-2 rounded-r-md transition-colors flex items-center justify-center">
                        <span class="material-symbols-outlined text-sm">send</span>
                    </button>
                </div>
            </div>
        </div>
        <!-- Copyright -->
        <div class="border-t border-surface-white/10">
            <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-6 text-center">
                <p class="font-caption text-caption text-surface-variant/70">
                    © 2024 TriHitaTour. The Sophisticated Explorer's Choice. Semua hak cipta dilindungi undang-undang.
                </p>
            </div>
        </div>
    </footer>
    <script>
        // Simple navbar scroll effect
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('navbar');
            if (window.scrollY > 50) {
                nav.classList.add('shadow-md');
                nav.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
            } else {
                nav.classList.remove('shadow-md');
                nav.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
            }
        });

        // Mobile menu toggle
        const mobileBtn = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');
        if (mobileBtn && mobileMenu) {
            mobileBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
    </script>
    @livewireScripts
</body>
</html>
