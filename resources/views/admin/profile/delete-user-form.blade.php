<section>
    <header>
        <h5 class="fw-bold text-danger">{{ __('Hapus Akun') }}</h5>
        <p class="text-muted small">
            {{ __('Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Sebelum menghapus, harap unduh data atau informasi apa pun yang ingin Anda simpan.') }}
        </p>
    </header>

    <button type="button" class="btn btn-danger w-100 mt-3" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ __('Hapus Akun') }}
    </button>

    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="{{ route('admin.profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="confirmUserDeletionModalLabel">{{ __('Konfirmasi Penghapusan Akun') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p>
                            {{ __('Apakah Anda yakin ingin menghapus akun Anda? Setelah akun dihapus, semua datanya akan hilang selamanya. Masukkan password Anda untuk mengonfirmasi.') }}
                        </p>

                        <div class="mt-3">
                            <label for="password_delete" class="form-label">{{ __('Password') }}</label>
                            <input
                                id="password_delete"
                                name="password"
                                type="password"
                                class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                placeholder="{{ __('Masukkan password Anda') }}"
                            />
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                        <button type="submit" class="btn btn-danger ms-3">
                            {{ __('Hapus Akun') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>