@foreach($users as $user)
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
    {{-- PERBAIKAN FINAL DAN UTAMA ADA DI BARIS DI BAWAH INI --}}
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-content border border-1 border-primary rounded-4 shadow">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                </div>
                <div class="mb-3">
                    <label>Role</label>
                    <select name="role" class="form-select" required>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ $user->hasRole($role) ? 'selected' : '' }}>
                                {{ ucfirst($role) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Devisi</label>
                    <select name="devisi_id" class="form-select">
                        <option value="">-- Tidak ada Devisi --</option>
                        @foreach($devisis as $id => $nama_devisi)
                            <option value="{{ $id }}" {{ $user->devisi_id == $id ? 'selected' : '' }}>
                                {{ $nama_devisi }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer border-top-0 d-flex justify-content-end">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Update</button>
            </div>
        </div>
    </form>
</div>
@endforeach

<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrCodeModalLabel">QR Code for</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p id="userNameInModal" class="fw-bold fs-5"></p>
                <div id="qrCodeContainer"></div>
                <small class="text-muted mt-2 d-block">This QR code is unique to the user.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>