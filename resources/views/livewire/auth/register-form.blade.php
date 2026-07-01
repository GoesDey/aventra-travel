<div class="min-h-screen flex items-center justify-center font-body-md text-body-md text-on-surface bg-background p-margin-mobile md:p-margin-desktop relative overflow-hidden">
    <div class="absolute inset-0 z-0 pointer-events-none">
        <div class="absolute top-[-10%] left-[-5%] w-96 h-96 bg-primary-fixed opacity-40 rounded-full blur-3xl"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-80 h-80 bg-secondary-fixed opacity-30 rounded-full blur-3xl"></div>
    </div>
    
    <main class="w-full max-w-[480px] bg-surface-white rounded-[24px] ambient-shadow p-8 md:p-12 z-10 border border-outline-variant/30 backdrop-blur-xl relative">
        <div class="text-center mb-8">
            <h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-oceanic-deep mb-2">Aventra Travel</h1>
            <p class="font-body-md text-body-md text-on-surface-variant">Create your account to start exploring.</p>
        </div>
        
        <form wire:submit.prevent="register" class="space-y-6">
            <div class="space-y-2">
                <label class="font-label-bold text-label-bold text-on-surface block" for="name">Full Name</label>
                <input wire:model="name" class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 font-body-md text-body-md text-on-surface focus:ring-2 focus:ring-primary focus:outline-none transition-shadow" id="name" placeholder="John Doe" required type="text">
                @error('name') <span class="text-error text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="font-label-bold text-label-bold text-on-surface block" for="email">Email Address</label>
                <input wire:model="email" class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 font-body-md text-body-md text-on-surface focus:ring-2 focus:ring-primary focus:outline-none transition-shadow" id="email" placeholder="explorer@example.com" required type="email">
                @error('email') <span class="text-error text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div class="space-y-2">
                <label class="font-label-bold text-label-bold text-on-surface block" for="password">Password</label>
                <input wire:model="password" class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 font-body-md text-body-md text-on-surface focus:ring-2 focus:ring-primary focus:outline-none transition-shadow" id="password" placeholder="••••••••" required type="password">
                @error('password') <span class="text-error text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="font-label-bold text-label-bold text-on-surface block" for="password_confirmation">Confirm Password</label>
                <input wire:model="password_confirmation" class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 font-body-md text-body-md text-on-surface focus:ring-2 focus:ring-primary focus:outline-none transition-shadow" id="password_confirmation" placeholder="••••••••" required type="password">
            </div>
            
            <button class="w-full bg-primary hover:bg-primary-container text-on-primary font-label-bold text-label-bold py-4 rounded-full transition-all duration-300 transform active:scale-[0.98] ambient-shadow flex items-center justify-center gap-2" type="submit">
                Sign Up
            </button>
        </form>
        
        <p class="text-center mt-8 font-body-md text-body-md text-on-surface-variant">
            Already have an account? 
            <a class="text-primary font-label-bold text-label-bold hover:underline underline-offset-4 ml-1" href="{{ route('login') }}">Sign in</a>
        </p>
    </main>
</div>
