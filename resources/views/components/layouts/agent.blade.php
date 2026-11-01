<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Agent Portal - TriHitaTour' }}</title>
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
</head>
<body class="font-body-md bg-surface-container-lowest text-on-surface min-h-screen flex antialiased">
    
    <!-- Sidebar -->
    <aside class="hidden md:flex flex-col w-64 bg-surface-white border-r border-outline-variant/30 shrink-0 h-screen sticky top-0 z-20">
        <div class="p-6 border-b border-outline-variant/30 flex items-center gap-3">
            <span class="material-symbols-outlined fill text-tropical-teal text-3xl">travel_explore</span>
            <div>
                <span class="font-display-lg text-[20px] font-bold text-oceanic-deep tracking-wide block leading-tight">TRIHITATOUR</span>
                <span class="text-[11px] font-bold text-tropical-teal uppercase tracking-widest">Portal Agen</span>
            </div>
        </div>

        <nav class="flex-1 py-6 px-4 space-y-2 overflow-y-auto">
            <div class="text-xs font-bold text-outline uppercase tracking-wider mb-2 px-3">Menu Agen</div>
            
            <a href="{{ route('agent.packages.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('agent.packages.*') ? 'bg-tropical-teal/10 text-tropical-teal font-bold' : 'text-on-surface-variant hover:bg-surface-container-low transition-colors' }}">
                <span class="material-symbols-outlined {{ request()->routeIs('agent.packages.*') ? 'fill' : '' }}">tour</span>
                <span class="font-label-bold text-sm">Kelola Paket</span>
            </a>

            <a href="{{ route('packages.index') }}" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors">
                <span class="material-symbols-outlined">visibility</span>
                <span class="font-label-bold text-sm">Lihat Website</span>
            </a>
        </nav>

        <div class="p-4 border-t border-outline-variant/30">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-error hover:bg-error-container/50 transition-colors">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="font-label-bold text-sm">Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col min-h-screen min-w-0 bg-surface-container-lowest">
        
        <!-- Header -->
        <header class="h-[84.8px] py-6 bg-surface-white border-b border-outline-variant/30 flex items-center justify-between px-6 md:px-8 flex-shrink-0 z-10 shadow-sm sticky top-0">
            <div class="flex items-center gap-4">
                <a href="{{ route('agent.packages.index') }}" class="md:hidden text-tropical-teal flex items-center gap-2">
                    <span class="material-symbols-outlined fill text-2xl">travel_explore</span>
                    <span class="font-bold text-sm">Portal Agen</span>
                </a>
                <h1 class="font-headline-md text-[22px] font-bold text-oceanic-deep hidden md:block">
                    {{ $title ?? 'Dashboard Agen' }}
                </h1>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- Agent Profile -->
                <div class="flex items-center gap-3 border-l border-outline-variant/30 pl-4 sm:pl-6 group">
                    <div class="w-10 h-10 rounded-full bg-tropical-teal text-surface-white flex items-center justify-center font-bold shadow-sm">
                        {{ substr(auth()->user()->agent->agent_name ?? auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="flex flex-col hidden sm:flex">
                        <span class="font-label-bold text-sm text-on-surface group-hover:text-tropical-teal transition-colors">
                            {{ auth()->user()->agent->agent_name ?? auth()->user()->name }}
                        </span>
                        <span class="font-caption text-[11px] text-outline">
                            Agen Travel ({{ auth()->user()->status }})
                        </span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-6 md:px-8 pt-6">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
                    <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
                    <span class="material-symbols-outlined text-rose-600">error</span>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif
        </div>

        <!-- Content Body -->
        <div class="flex-1 overflow-y-auto p-6 md:p-8">
            {{ $slot }}
        </div>
    </main>

    @stack('scripts')
</body>
</html>
