<div class="min-h-screen flex items-center justify-center font-body-md text-body-md text-on-surface bg-background p-margin-mobile md:p-margin-desktop relative overflow-hidden">
    <!-- Decorative Background Elements -->
    <div class="absolute inset-0 z-0 pointer-events-none">
        <div class="absolute top-[-10%] right-[-5%] w-96 h-96 bg-primary-fixed opacity-40 rounded-full blur-3xl"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-80 h-80 bg-secondary-fixed opacity-30 rounded-full blur-3xl"></div>
    </div>
    
    <!-- Main Content Container -->
    <main class="w-full max-w-[480px] bg-surface-white rounded-[24px] ambient-shadow p-8 md:p-12 z-10 border border-outline-variant/30 backdrop-blur-xl relative">
        <!-- Brand / Header -->
        <div class="text-center mb-8">
            <h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-oceanic-deep mb-2">Aventra Travel</h1>
            <p class="font-body-md text-body-md text-on-surface-variant">Welcome back. Your next adventure awaits.</p>
        </div>
        
        <!-- Login Form -->
        <form wire:submit.prevent="login" class="space-y-6">
            @if (session()->has('error'))
                <div class="bg-error-container text-on-error-container p-4 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Email Input -->
            <div class="space-y-2">
                <label class="font-label-bold text-label-bold text-on-surface block" for="email">Email Address</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-outline">@</span>
                    <input wire:model="email" class="w-full bg-surface-container-low border-none rounded-lg pl-12 pr-4 py-3 font-body-md text-body-md text-on-surface focus:ring-2 focus:ring-primary focus:outline-none transition-shadow" id="email" placeholder="explorer@example.com" required type="email">
                </div>
                @error('email') <span class="text-error text-sm">{{ $message }}</span> @enderror
            </div>
            
            <!-- Password Input -->
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <label class="font-label-bold text-label-bold text-on-surface block" for="password">Password</label>
                </div>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-outline">#</span>
                    <input wire:model="password" class="w-full bg-surface-container-low border-none rounded-lg pl-12 pr-12 py-3 font-body-md text-body-md text-on-surface focus:ring-2 focus:ring-primary focus:outline-none transition-shadow" id="password" placeholder="••••••••" required type="password">
                </div>
                @error('password') <span class="text-error text-sm">{{ $message }}</span> @enderror
            </div>
            
            <!-- Primary Action -->
            <button class="w-full bg-tropical-teal hover:bg-secondary text-on-primary font-label-bold text-label-bold py-4 rounded-full transition-all duration-300 transform active:scale-[0.98] ambient-shadow flex items-center justify-center gap-2" type="submit">
                Sign In
            </button>
        </form>
        
        <!-- Sign Up Link -->
        <p class="text-center mt-8 font-body-md text-body-md text-on-surface-variant">
            Don't have an account? 
            <a class="text-tropical-teal font-label-bold text-label-bold hover:underline underline-offset-4 ml-1" href="{{ route('register') }}">Sign up</a>
        </p>
    </main>
</div>
