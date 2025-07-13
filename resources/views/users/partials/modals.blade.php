{{-- Modal untuk Edit User --}}
@foreach($users as $user)
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
    {{-- PERBAIKAN UTAMA: Menambahkan wrapper <div class="modal-dialog"> --}}
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            {{-- Form sekarang berada di dalam .modal-content untuk struktur yang valid --}}
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}"><i class="bi bi-pencil-square me-2"></i>Edit Pengguna: {{ $user->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name-{{$user->id}}" class="form-label">Nama Lengkap</label>
                        <input type="text" id="name-{{$user->id}}" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="email-{{$user->id}}" class="form-label">Email</label>
                        <input type="email" id="email-{{$user->id}}" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="role-{{$user->id}}" class="form-label">Role</label>
                        <select id="role-{{$user->id}}" name="role" class="form-select" required>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ $user->hasRole($role) ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="devisi_id-{{$user->id}}" class="form-label">Devisi</label>
                        <select id="devisi_id-{{$user->id}}" name="devisi_id" class="form-select">
                            <option value="">-- Tidak Ada Devisi --</option>
                            @foreach($devisis as $id => $nama_devisi)
                                <option value="{{ $id }}" {{ $user->devisi_id == $id ? 'selected' : '' }}>
                                    {{ $nama_devisi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- Modal untuk Menampilkan QR Code (Struktur sudah benar, tidak perlu diubah) --}}
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrCodeModalLabel">QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p id="userNameInModal" class="fw-bold fs-5 mb-2"></p>
                <div id="qrCodeContainer" style="min-height: 150px;"></div>
                <small class="text-muted mt-2 d-block">QR code ini unik untuk setiap pengguna.</small>
            </div>
        </div>
    </div>
</div>