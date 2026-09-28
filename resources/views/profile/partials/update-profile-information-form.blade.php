<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
        
        <!-- ===== TAMBAHKAN BAGIAN TTD DIGITAL PROFIL DI SINI ===== -->
        @if(in_array(auth()->user()->role, ['hrd', 'supervisor']))
            <div class="pt-2 border-t mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanda Tangan Digital Resmi</label>
                
                @if(auth()->user()->signature_pad)
                    <div class="mb-2">
                        <span class="text-xs text-gray-500">Tanda tangan yang sedang aktif:</span><br>
                        <img src="{{ auth()->user()->signature_pad }}" alt="TTD Aktif" class="h-16 border rounded bg-white p-1 mt-1">
                    </div>
                @endif

                <div class="border rounded-lg p-2 bg-gray-50 inline-block">
                    <canvas id="profile-sig-pad" class="border rounded bg-white w-64 h-28"></canvas>
                    <div class="mt-1">
                        <button type="button" id="clear-profile-sig" class="text-xs text-red-600 underline">Bersihkan</button>
                    </div>
                </div>
                <input type="hidden" name="signature_pad" id="profile_sig_input">
            </div>

            <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const canvas = document.getElementById('profile-sig-pad');
                    if (!canvas) return;
                    const pad = new SignaturePad(canvas, { backgroundColor: 'rgba(255, 255, 255, 0)', penColor: 'rgb(0,0,0)' });
                    
                    document.getElementById('clear-profile-sig').addEventListener('click', () => pad.clear());
                    
                    const form = canvas.closest('form');
                    form.addEventListener('submit', function () {
                        if (!pad.isEmpty()) {
                            document.getElementById('profile_sig_input').value = pad.toDataURL('image/png');
                        }
                    });
                });
            </script>
        @endif
        <!-- ======================================================= -->

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
