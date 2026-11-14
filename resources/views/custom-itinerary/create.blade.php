<x-layouts.app title="Build Custom Itinerary - TriHitaTour">
    <div class="min-h-screen bg-surface-container-lowest pt-24 pb-16 px-margin-mobile md:px-margin-desktop">
        <div class="max-w-container-max mx-auto space-y-6">

            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-2 text-xs md:text-sm text-outline">
                <a href="{{ route('home') }}" class="hover:text-tropical-teal transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('packages.index') }}" class="hover:text-tropical-teal transition-colors">Tour Packages</a>
                <span>/</span>
                <span class="text-on-surface font-semibold">Custom Itinerary</span>
            </nav>

            <!-- Header -->
            <div class="mb-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-tropical-teal/10 text-tropical-teal mb-2">
                    <span class="material-symbols-outlined text-sm">tune</span>
                    <span>Tailor-Made Bali Experience</span>
                </span>
                <h1 class="font-display-lg text-2xl md:text-4xl font-bold text-oceanic-deep tracking-tight">
                    Build Your Custom Itinerary
                </h1>
                <p class="mt-2 text-sm md:text-base text-on-surface-variant max-w-2xl leading-relaxed">
                    Select your preferred region, choose an authorized local agent, and customize your travel destinations. Driving distance and travel duration are calculated automatically, with full real-time overtime transparency.
                </p>
            </div>

            <!-- Validation Errors Alert -->
            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl shadow-sm">
                    <div class="flex items-center gap-2 font-bold text-sm mb-1">
                        <span class="material-symbols-outlined text-base text-rose-600">error</span>
                        <span>Please correct the following errors:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Livewire Component: Custom Itinerary Builder -->
            <livewire:customer.custom-itinerary-builder
                :initial-region="request('region')"
                :initial-package-id="request('package_id')"
            />

        </div>
    </div>
</x-layouts.app>
