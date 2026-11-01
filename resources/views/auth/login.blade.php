<x-layouts.auth title="Login - TriHitaTour">
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
                <h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-oceanic-deep mb-2">TriHitaTour</h1>
                <p class="font-body-md text-body-md text-on-surface-variant">Welcome back. Your next adventure awaits.</p>
            </div>
            
            <!-- Login Form Component -->
            <livewire:auth.login-form />
        </main>
    </div>
</x-layouts.auth>
