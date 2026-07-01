<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Aventra Travel' }}</title>
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
    <nav class="fixed top-0 w-full z-50 bg-glass-fill backdrop-blur-md dark:bg-oceanic-deep/70 border-b border-outline-variant/30 shadow-sm transition-all duration-300" id="navbar" style="background-color: rgba(255, 255, 255, 0.7);">
        <div class="flex justify-between items-center px-margin-mobile md:px-margin-desktop py-4 max-w-container-max mx-auto">
            <!-- Brand -->
            <a href="{{ route('home') }}" class="font-display-lg-mobile md:font-display-lg text-display-lg-mobile md:text-display-lg font-bold text-oceanic-deep dark:text-primary-fixed tracking-tight hover:opacity-80 transition-opacity">
                Aventra Travel
            </a>
            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-tropical-teal border-b-2 border-tropical-teal pb-1 font-bold' : 'text-on-surface-variant hover:text-tropical-teal transition-colors duration-200' }} font-body-md text-body-md">Home</a>
                <a href="{{ route('packages.index') }}" class="{{ request()->routeIs('packages.*') ? 'text-tropical-teal border-b-2 border-tropical-teal pb-1 font-bold' : 'text-on-surface-variant hover:text-tropical-teal transition-colors duration-200' }} font-body-md text-body-md">Packages</a>
                <a href="{{ route('history') }}" class="{{ request()->routeIs('history') ? 'text-tropical-teal border-b-2 border-tropical-teal pb-1 font-bold' : 'text-on-surface-variant hover:text-tropical-teal transition-colors duration-200' }} font-body-md text-body-md">History</a>
            </div>
            <!-- Actions -->
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.bookings') }}" class="text-oceanic-deep font-label-bold text-label-bold hover:text-tropical-teal transition-colors px-4 py-2">Admin Dashboard</a>
                    @endif
                    <div class="w-10 h-10 rounded-full bg-primary-fixed text-primary flex items-center justify-center font-bold shadow">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-error font-label-bold text-label-bold hover:underline px-4 py-2">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-oceanic-deep font-label-bold text-label-bold hover:text-tropical-teal transition-colors px-4 py-2">Sign In</a>
                    <a href="{{ route('register') }}" class="bg-tropical-teal text-surface-white font-label-bold text-label-bold px-6 py-2.5 rounded-full hover:bg-secondary transition-colors shadow-md hover:shadow-lg transform hover:-translate-y-0.5 active:scale-95 duration-200">Register</a>
                @endauth
            </div>
            <!-- Mobile Menu Toggle -->
            <button class="md:hidden text-oceanic-deep p-2">
                <span class="material-symbols-outlined" style="font-size: 28px;">menu</span>
            </button>
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
                    <span class="">Aventra</span>
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
                    © 2024 Aventra Travel. The Sophisticated Explorer's Choice. Semua hak cipta dilindungi undang-undang.
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
                nav.style.backgroundColor = 'rgba(255, 255, 255, 0.7)';
            }
        });
    </script>
    @livewireScripts
</body>
</html>
