<div class="min-h-screen flex items-center justify-center font-body-md text-body-md text-on-surface bg-background py-10 px-margin-mobile md:px-margin-desktop relative overflow-hidden">
    <div class="absolute inset-0 z-0 pointer-events-none">
        <div class="absolute top-[-10%] left-[-5%] w-96 h-96 bg-primary-fixed opacity-40 rounded-full blur-3xl"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-80 h-80 bg-secondary-fixed opacity-30 rounded-full blur-3xl"></div>
    </div>

    <main class="w-full {{ $accountType === 'partner' ? 'max-w-[640px]' : 'max-w-[480px]' }} transition-all duration-300 bg-surface-white rounded-[24px] ambient-shadow p-8 md:p-10 z-10 border border-outline-variant/30 backdrop-blur-xl relative">
        <div class="text-center mb-6">
            <h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-oceanic-deep mb-2">TriHitaTour</h1>
            <p class="font-body-md text-body-md text-on-surface-variant">
                {{ $accountType === 'partner' ? __('Register your travel agency to partner with TriHitaTour.') : __('Create your account to start exploring.') }}
            </p>
        </div>

        <!-- Account Type Tabs: Traveler vs Travel Partner -->
        <div class="grid grid-cols-2 gap-2 p-1.5 rounded-2xl bg-surface-container-low border border-outline-variant/30 mb-7" role="tablist">
            <button type="button"
                    wire:click="setAccountType('traveler')"
                    role="tab"
                    aria-selected="{{ $accountType === 'traveler' ? 'true' : 'false' }}"
                    class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl font-label-bold text-sm transition-all cursor-pointer {{ $accountType === 'traveler' ? 'bg-surface-white text-oceanic-deep shadow-sm border border-outline-variant/20' : 'text-on-surface-variant hover:text-oceanic-deep' }}">
                <span class="material-symbols-outlined text-[18px]">person</span>
                <span>{{ __('Traveler') }}</span>
            </button>

            <button type="button"
                    wire:click="setAccountType('partner')"
                    role="tab"
                    aria-selected="{{ $accountType === 'partner' ? 'true' : 'false' }}"
                    class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl font-label-bold text-sm transition-all cursor-pointer {{ $accountType === 'partner' ? 'bg-tropical-teal text-white shadow-sm' : 'text-on-surface-variant hover:text-oceanic-deep' }}">
                <span class="material-symbols-outlined text-[18px]">storefront</span>
                <span>{{ __('Travel Partner') }}</span>
            </button>
        </div>

        <form wire:submit.prevent="register" class="space-y-5">
            <!-- Account Credentials Section -->
            <div class="grid grid-cols-1 {{ $accountType === 'partner' ? 'sm:grid-cols-2' : '' }} gap-4">
                <div class="space-y-1.5">
                    <label class="font-label-bold text-label-bold text-on-surface block" for="name">{{ __('Full Name') }}</label>
                    <input wire:model="name" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 font-body-md text-body-md text-on-surface focus:ring-2 focus:ring-primary focus:outline-none transition-shadow" id="name" placeholder="John Doe" required type="text">
                    @error('name') <span class="text-error text-xs block">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="font-label-bold text-label-bold text-on-surface block" for="email">{{ __('Email Address') }}</label>
                    <input wire:model="email" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 font-body-md text-body-md text-on-surface focus:ring-2 focus:ring-primary focus:outline-none transition-shadow" id="email" placeholder="explorer@example.com" required type="email">
                    @error('email') <span class="text-error text-xs block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 {{ $accountType === 'partner' ? 'sm:grid-cols-2' : '' }} gap-4">
                <div class="space-y-1.5" x-data="{ showPassword: false }">
                    <label class="font-label-bold text-label-bold text-on-surface block" for="password">{{ __('Password') }}</label>
                    <div class="relative">
                        <input wire:model="password"
                               :type="showPassword ? 'text' : 'password'"
                               class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg pl-4 pr-11 py-3 font-body-md text-body-md text-on-surface focus:ring-2 focus:ring-primary focus:outline-none transition-shadow"
                               id="password"
                               placeholder="••••••••"
                               required>
                        <button type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-oceanic-deep transition-colors p-1 rounded-md focus:outline-none flex items-center justify-center cursor-pointer"
                                :title="showPassword ? '{{ __('Hide password') }}' : '{{ __('Show password') }}'"
                                aria-label="{{ __('Show or hide password') }}">
                            <span class="material-symbols-outlined text-[20px] select-none" x-text="showPassword ? 'visibility_off' : 'visibility'">
                                visibility
                            </span>
                        </button>
                    </div>
                    @error('password') <span class="text-error text-xs block">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1.5" x-data="{ showConfirmPassword: false }">
                    <label class="font-label-bold text-label-bold text-on-surface block" for="password_confirmation">{{ __('Confirm Password') }}</label>
                    <div class="relative">
                        <input wire:model="password_confirmation"
                               :type="showConfirmPassword ? 'text' : 'password'"
                               class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg pl-4 pr-11 py-3 font-body-md text-body-md text-on-surface focus:ring-2 focus:ring-primary focus:outline-none transition-shadow"
                               id="password_confirmation"
                               placeholder="••••••••"
                               required>
                        <button type="button"
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-oceanic-deep transition-colors p-1 rounded-md focus:outline-none flex items-center justify-center cursor-pointer"
                                :title="showConfirmPassword ? '{{ __('Hide password') }}' : '{{ __('Show password') }}'"
                                aria-label="{{ __('Show or hide confirm password') }}">
                            <span class="material-symbols-outlined text-[20px] select-none" x-text="showConfirmPassword ? 'visibility_off' : 'visibility'">
                                visibility
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Partner-Only Fields -->
            @if($accountType === 'partner')
                <div class="pt-4 border-t border-outline-variant/30 space-y-5">
                    <div class="flex items-center gap-2 text-oceanic-deep">
                        <span class="material-symbols-outlined text-tropical-teal">business_center</span>
                        <h2 class="font-label-bold text-sm uppercase tracking-wider">{{ __('Business Information') }}</h2>
                    </div>

                    <div class="space-y-1.5">
                        <label class="font-label-bold text-label-bold text-on-surface block" for="business_name">{{ __('Business Name') }} <span class="text-error">*</span></label>
                        <input wire:model="business_name" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 font-body-md text-body-md text-on-surface focus:ring-2 focus:ring-tropical-teal focus:outline-none" id="business_name" placeholder="{{ __('e.g. PT Bali Indah Tour & Travel') }}" required type="text">
                        @error('business_name') <span class="text-error text-xs block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="font-label-bold text-label-bold text-on-surface block" for="whatsapp_number">{{ __('WhatsApp Number') }} <span class="text-error">*</span></label>
                            <input wire:model="whatsapp_number" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 font-body-md text-body-md text-on-surface focus:ring-2 focus:ring-tropical-teal focus:outline-none" id="whatsapp_number" placeholder="081234567890" required type="text">
                            @error('whatsapp_number') <span class="text-error text-xs block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="font-label-bold text-label-bold text-on-surface block" for="nib_number">{{ __('Business License / NIB') }} <span class="text-error">*</span></label>
                            <input wire:model="nib_number" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 font-body-md text-body-md text-on-surface focus:ring-2 focus:ring-tropical-teal focus:outline-none" id="nib_number" placeholder="1234567890123" required type="text">
                            @error('nib_number') <span class="text-error text-xs block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="font-label-bold text-label-bold text-on-surface block">{{ __('Bali Operating Regions') }} <span class="text-error">*</span></label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                            @foreach(['utara' => __('North Bali'), 'selatan' => __('South Bali'), 'timur' => __('East Bali'), 'barat' => __('West Bali')] as $key => $label)
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-outline-variant/40 bg-surface-container-lowest hover:border-tropical-teal cursor-pointer transition-colors has-[:checked]:border-tropical-teal has-[:checked]:bg-tropical-teal/10">
                                    <input type="checkbox" wire:model="operating_regions" value="{{ $key }}" class="rounded text-tropical-teal focus:ring-tropical-teal">
                                    <span class="text-xs font-semibold text-on-surface">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('operating_regions') <span class="text-error text-xs block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-3 border-t border-outline-variant/20 space-y-4">
                        <div class="flex items-center gap-2 text-oceanic-deep">
                            <span class="material-symbols-outlined text-tropical-teal">account_balance</span>
                            <h2 class="font-label-bold text-sm uppercase tracking-wider">{{ __('Bank Account Details') }}</h2>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="font-label-bold text-label-bold text-on-surface block" for="bank_name">{{ __('Bank Name') }} <span class="text-error">*</span></label>
                                <input wire:model="bank_name" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 font-body-md text-body-md text-on-surface focus:ring-2 focus:ring-tropical-teal focus:outline-none" id="bank_name" placeholder="BCA / Mandiri / BRI / BNI" required type="text">
                                @error('bank_name') <span class="text-error text-xs block">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label class="font-label-bold text-label-bold text-on-surface block" for="bank_account_number">{{ __('Bank Account Number') }} <span class="text-error">*</span></label>
                                <input wire:model="bank_account_number" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 font-body-md text-body-md text-on-surface focus:ring-2 focus:ring-tropical-teal focus:outline-none" id="bank_account_number" placeholder="1234567890" required type="text">
                                @error('bank_account_number') <span class="text-error text-xs block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="font-label-bold text-label-bold text-on-surface block" for="bank_account_name">{{ __('Account Holder Name') }} <span class="text-error">*</span></label>
                            <input wire:model="bank_account_name" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 font-body-md text-body-md text-on-surface focus:ring-2 focus:ring-tropical-teal focus:outline-none" id="bank_account_name" placeholder="{{ __('Name as shown on bank account') }}" required type="text">
                            @error('bank_account_name') <span class="text-error text-xs block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-2.5">
                        <span class="material-symbols-outlined text-amber-600 text-lg shrink-0">info</span>
                        <span>{{ __('Partner accounts require Super Admin verification before gaining access to the Partner Agent Portal.') }}</span>
                    </div>
                </div>
            @endif

            <button class="w-full {{ $accountType === 'partner' ? 'bg-tropical-teal hover:bg-oceanic-deep' : 'bg-primary hover:bg-primary-container' }} text-on-primary font-label-bold text-label-bold py-4 rounded-full transition-all duration-300 transform active:scale-[0.98] ambient-shadow flex items-center justify-center gap-2 cursor-pointer" type="submit" wire:loading.attr="disabled">
                <span>{{ $accountType === 'partner' ? __('Submit Partner Application') : __('Sign Up') }}</span>
            </button>
        </form>

        <p class="text-center mt-6 font-body-md text-body-md text-on-surface-variant">
            {{ __('Already have an account?') }}
            <a class="text-primary font-label-bold text-label-bold hover:underline underline-offset-4 ml-1" href="{{ route('login') }}">{{ __('Sign in') }}</a>
        </p>
    </main>
</div>
