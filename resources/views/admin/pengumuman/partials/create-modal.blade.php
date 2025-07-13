<div class="modal fade" id="createPengumumanModal" tabindex="-1" aria-labelledby="createPengumumanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.pengumuman.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createPengumumanModalLabel">Buat Pengumuman Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul Pengumuman</label>
                        <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul') }}" required>
                        @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    {{-- PERBAIKAN: Menggunakan 'isi' sebagai nama field --}}
                    <div class="mb-3">
                        <label for="isi" class="form-label">Isi Pengumuman</label>
                        <textarea class="form-control @error('isi') is-invalid @enderror" id="isi" name="isi" rows="5" required>{{ old('isi') }}</textarea>
                        @error('isi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="target" class="form-label">Target Pengumuman</label>
                            <select class="form-select @error('target') is-invalid @enderror" id="target" name="target" required>
                                <option value="semua" @if(old('target') == 'semua') selected @endif>Semua Anggota</option>
                                <option value="devisi" @if(old('target') == 'devisi') selected @endif>Divisi Tertentu</option>
                            </select>
                            @error('target') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3" id="devisiSelectDiv" style="{{ old('target') == 'devisi' ? 'display: block;' : 'display: none;' }}">
                            <label for="devisi_id" class="form-label">Pilih Divisi</label>
                            <select class="form-select @error('devisi_id') is-invalid @enderror" id="devisi_id" name="devisi_id">
                                <option value="">-- Pilih Divisi --</option>
                                {{-- PERBAIKAN: Menggunakan variabel $devisis yang benar --}}
                                @foreach($devisis as $d)
                                <option value="{{ $d->id }}" @if(old('devisi_id') == $d->id) selected @endif>{{ $d->nama_devisi }}</option>
                                @endforeach
                            </select>
                            @error('devisi_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="publish_at" class="form-label">Mulai Tayang</label>
                            <input type="text" class="form-control @error('publish_at') is-invalid @enderror" id="publish_at" name="publish_at" value="{{ old('publish_at') }}" placeholder="Pilih tanggal & waktu" required>
                             @error('publish_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="expires_at" class="form-label">Berhenti Tayang (Opsional)</label>
                            <input type="text" class="form-control @error('expires_at') is-invalid @enderror" id="expires_at" name="expires_at" value="{{ old('expires_at') }}" placeholder="Pilih tanggal & waktu">
                            <div class="form-text">Kosongkan jika ingin tayang selamanya.</div>
                            @error('expires_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send-fill me-1"></i> Terbitkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>