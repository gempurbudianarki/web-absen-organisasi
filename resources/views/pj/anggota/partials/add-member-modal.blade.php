{{-- Modal Tambah Anggota --}}
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('pj.anggota.add') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addMemberModalLabel">
                        <i class="bi bi-person-plus-fill me-2"></i>Tambah Anggota ke Devisi {{ $devisi->nama_devisi }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Pilih Calon Anggota</label>
                        <select name="user_id" id="user_id" class="form-select" required>
                            <option value="">-- Pilih dari daftar user tanpa devisi --</option>
                            @forelse($calon_anggota as $calon)
                                <option value="{{ $calon->id }}">{{ $calon->name }}</option>
                            @empty
                                <option value="" disabled>Semua anggota sudah memiliki devisi.</option>
                            @endforelse
                        </select>
                        <div class="form-text">
                            Hanya user dengan role "Anggota" yang belum tergabung di devisi manapun yang akan muncul di sini.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambahkan ke Devisi</button>
                </div>
            </form>
        </div>
    </div>
</div>