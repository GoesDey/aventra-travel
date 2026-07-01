<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Dashboard - Aventra Travel' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .material-symbols-outlined.fill {
            font-variation-settings: 'FILL' 1;
        }
    </style>
    @livewireStyles
</head>
<body class="font-body-md bg-surface-container-lowest text-on-surface min-h-screen flex antialiased">
    
    <!-- Sidebar -->
    <aside class="hidden md:flex flex-col w-64 bg-surface-white border-r border-outline-variant/30 shrink-0 h-screen sticky top-0 z-20">
        <div class="p-6 border-b border-outline-variant/30 flex items-center gap-3">
            <span class="material-symbols-outlined fill text-oceanic-deep text-3xl">travel_explore</span>
            <span class="font-display-lg text-[24px] font-bold text-oceanic-deep tracking-wide">AVENTRA</span>
        </div>
        <nav class="flex-1 py-6 px-4 space-y-2 overflow-y-auto">
            <div class="text-xs font-bold text-outline uppercase tracking-wider mb-2 px-3">Overview</div>
            <a href="{{ route('admin.finance') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.finance') ? 'bg-primary-container/20 text-oceanic-deep font-bold' : 'text-on-surface-variant hover:bg-surface-container-low transition-colors' }}">
                <span class="material-symbols-outlined {{ request()->routeIs('admin.finance') ? 'fill' : '' }}">dashboard</span>
                <span class="font-label-bold text-sm">Finance</span>
            </a>
            <a href="{{ route('admin.bookings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.bookings') ? 'bg-primary-container/20 text-oceanic-deep font-bold' : 'text-on-surface-variant hover:bg-surface-container-low transition-colors' }}">
                <span class="material-symbols-outlined {{ request()->routeIs('admin.bookings') ? 'fill' : '' }}">receipt_long</span>
                <span class="font-label-bold text-sm">Bookings</span>
            </a>
            
            <div class="text-xs font-bold text-outline uppercase tracking-wider mt-6 mb-2 px-3">Management</div>
            <a href="{{ route('admin.destinations') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.destinations') ? 'bg-primary-container/20 text-oceanic-deep font-bold' : 'text-on-surface-variant hover:bg-surface-container-low transition-colors' }}">
                <span class="material-symbols-outlined {{ request()->routeIs('admin.destinations') ? 'fill' : '' }}">location_on</span>
                <span class="font-label-bold text-sm">Destinations</span>
            </a>
            <a href="{{ route('admin.packages') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.packages') ? 'bg-primary-container/20 text-oceanic-deep font-bold' : 'text-on-surface-variant hover:bg-surface-container-low transition-colors' }}">
                <span class="material-symbols-outlined {{ request()->routeIs('admin.packages') ? 'fill' : '' }}">tour</span>
                <span class="font-label-bold text-sm">Packages</span>
            </a>
        </nav>
        <div class="p-4 border-t border-outline-variant/30">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-error hover:bg-error-container/50 transition-colors">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="font-label-bold text-sm">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col min-h-screen min-w-0 bg-surface-container-lowest">
        
        @php
            $pageTitle = 'Dashboard';
            if(request()->routeIs('admin.finance')) $pageTitle = 'Financial Overview';
            if(request()->routeIs('admin.destinations')) $pageTitle = 'Destination Management';
            if(request()->routeIs('admin.packages')) $pageTitle = 'Package Management';
            if(request()->routeIs('admin.bookings')) $pageTitle = 'Booking Management';
        @endphp

        <!-- Top Navigation Bar -->
        <header class="h-20 bg-surface-white border-b border-outline-variant/30 flex items-center justify-between px-8 flex-shrink-0 z-10 shadow-sm sticky top-0">
            <div class="flex items-center gap-4">
                <button class="md:hidden text-on-surface-variant">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <h1 class="font-headline-md text-[24px] text-oceanic-deep hidden md:block">{{ $pageTitle }}</h1>
            </div>
            
            <div class="flex items-center gap-6">
                <!-- Notifications -->
                <button class="relative p-2 text-on-surface-variant hover:text-oceanic-deep hover:bg-surface-container-low rounded-full transition-colors">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-soft-coral rounded-full border-2 border-surface-white"></span>
                </button>
                
                <!-- Admin Profile -->
                <div class="flex items-center gap-3 border-l border-outline-variant/30 pl-6 group">
                    <div class="w-10 h-10 rounded-full bg-oceanic-deep text-surface-white flex items-center justify-center font-bold shadow-sm">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="flex flex-col hidden sm:flex">
                        <span class="font-label-bold text-sm text-on-surface group-hover:text-oceanic-deep transition-colors">{{ auth()->user()->name ?? 'Admin' }}</span>
                        <span class="font-caption text-[11px] text-outline">{{ auth()->user()->email ?? 'admin@aventra.com' }}</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="flex-1 overflow-y-auto p-6 md:p-8">
            {{ $slot }}
        </div>
    </main>

    @livewireScripts
</body>
</html>
