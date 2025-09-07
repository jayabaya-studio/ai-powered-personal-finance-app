<section>
    <header>
        <h2 class="text-lg font-medium text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    {{-- [DIUBAH] Tambahkan enctype untuk mendukung unggah file --}}
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- [BARU] Bagian Foto Profil -->
        <div x-data="{photoName: null, photoPreview: null}">
            <label for="profile_photo" class="block text-sm font-medium text-gray-300">Profile Photo</label>
            <!-- Input File Tersembunyi -->
            <input type="file" id="profile_photo" name="profile_photo" class="hidden"
                   x-ref="photo"
                   x-on:change="
                       photoName = $refs.photo.files[0].name;
                       const reader = new FileReader();
                       reader.onload = (e) => {
                           photoPreview = e.target.result;
                       };
                       reader.readAsDataURL($refs.photo.files[0]);
                   " />

            <div class="mt-2 flex items-center gap-4">
                <!-- Foto Profil Saat Ini -->
                <img :src="photoPreview ?? '{{ $user->profile_photo_url }}'" alt="{{ $user->name }}" class="h-20 w-20 rounded-full object-cover">
                <!-- Tombol untuk Memilih Foto Baru -->
                <button type="button" class="rounded-md bg-white/10 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-white/20" x-on:click.prevent="$refs.photo.click()">
                    Change Photo
                </button>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
        </div>


        <!-- Nama -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-200">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="underline text-sm text-gray-400 hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- [BARU] Zona Waktu -->
        <div>
            <x-input-label for="timezone" :value="__('Timezone')" />
            <select id="timezone" name="timezone" class="mt-1 block w-full border-white/20 bg-white/5 text-gray-200 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm placeholder-gray-500">
                {{-- Daftar zona waktu bisa ditambahkan di sini --}}
                <option value="Asia/Jakarta" @selected(old('timezone', $user->timezone) == 'Asia/Jakarta')>Jakarta (WIB)</option>
                <option value="Asia/Makassar" @selected(old('timezone', $user->timezone) == 'Asia/Makassar')>Makassar (WITA)</option>
                <option value="Asia/Jayapura" @selected(old('timezone', $user->timezone) == 'Asia/Jayapura')>Jayapura (WIT)</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('timezone')" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-input-label for="payday" :value="__('Payday Date')" />
                <x-text-input id="payday" name="payday" type="number" min="1" max="31" class="mt-1 block w-full" :value="old('payday', $user->payday)" placeholder="e.g., 25" />
                <x-input-error class="mt-2" :messages="$errors->get('payday')" />
            </div>
            <div>
                <x-input-label for="income_source" :value="__('Primary Income Source')" />
                <select id="income_source" name="income_source" class="mt-1 block w-full border-white/20 bg-white/5 text-gray-200 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm">
                    <option value="">Select source</option>
                    <option value="Salary" @selected(old('income_source', $user->income_source) == 'Salary')>Salary</option>
                    <option value="Freelance" @selected(old('income_source', $user->income_source) == 'Freelance')>Freelance</option>
                    <option value="Business" @selected(old('income_source', $user->income_source) == 'Business')>Business</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('income_source')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-400">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>
