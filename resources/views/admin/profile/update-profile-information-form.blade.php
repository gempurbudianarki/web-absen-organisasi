<section>
    <header>
        <h5 class="fw-bold">{{ __('Informasi Profil') }}</h5>
        <p class="text-muted small">
            {{ __("Perbarui informasi profil dan alamat email akun Anda.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('admin.profile.update') }}" class="mt-4">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Nama Lengkap') }}</label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="form-text mt-2">
                    {{ __('Alamat email Anda belum terverifikasi.') }}
                    <button type="submit" form="send-verification" class="btn btn-link p-0 m-0 align-baseline">
                        {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                    </button>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success mt-2 p-2 small">
                        {{ __('Link verifikasi baru telah dikirim ke alamat email Anda.') }}
                    </div>
                @endif
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save-fill me-1"></i> {{ __('Simpan') }}
            </button>
        </div>
    </form>
</section>