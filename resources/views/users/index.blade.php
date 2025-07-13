@extends('layouts.admin')

@section('title', 'Manajemen User')

@section('content')
<div class="container-fluid">
    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    {{-- Card Filter --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3"><i class="bi bi-funnel-fill me-2"></i>Filter Pengguna</h5>
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="role" class="form-label">Berdasarkan Role</label>
                    <select name="role" id="role" class="form-select">
                        <option value="">Semua Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="devisi_id" class="form-label">Berdasarkan Devisi</label>
                    <select name="devisi_id" id="devisi_id" class="form-select">
                        <option value="">Semua Devisi</option>
                        @foreach($devisis as $id => $nama_devisi)
                            <option value="{{ $id }}" {{ request('devisi_id') == $id ? 'selected' : '' }}>{{ $nama_devisi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Terapkan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Card Tabel Pengguna --}}
    <form action="{{ route('admin.users.bulk_action') }}" method="POST" id="bulkActionForm" onsubmit="return confirm('Apakah Anda yakin ingin melakukan aksi ini pada item terpilih?');">
        @csrf
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>Daftar Pengguna</h5>
                <a href="{{ route('admin.register.form') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-person-plus-fill me-1"></i> Register User Baru
                </a>
            </div>
            <div class="card-body">
                {{-- Kontrol Aksi Massal (Muncul saat ada checkbox terpilih) --}}
                <div id="bulkActionControls" class="d-none align-items-center gap-3 mb-3 p-2 rounded" style="background-color: #e9ecef;">
                    <span class="fw-bold small">Aksi Massal:</span>
                    <select name="action" id="bulkActionSelect" class="form-select form-select-sm" style="width: auto;">
                        <option value="">Pilih Aksi...</option>
                        <option value="change_devisi">Pindahkan Devisi</option>
                        <option value="delete">Hapus Pengguna</option>
                    </select>
                    <select name="devisi_id" id="bulkDevisiSelect" class="form-select form-select-sm d-none" style="width: auto;">
                        <option value="">Pilih Devisi Tujuan...</option>
                        @foreach($devisis as $id => $nama_devisi)
                            <option value="{{ $id }}">{{ $nama_devisi }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-info">Terapkan</button>
                    <span id="selectedCount" class="small text-muted ms-auto">0 item terpilih</span>
                </div>

                {{-- Tabel Pengguna --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 1%;"><input type="checkbox" id="selectAll"></th>
                                <th>No.</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Devisi</th>
                                <th class="text-center" style="width:150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td><input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox"></td>
                                    <td>{{ $loop->iteration + $users->firstItem() - 1 }}</td>
                                    <td><a href="{{ route('admin.users.show', $user->id) }}">{{ $user->name }}</a></td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $user->getRoleNames()->first() ?? 'No Role' }}</span>
                                    </td>
                                    <td>{{ $user->devisi->nama_devisi ?? '-' }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#qrCodeModal" data-user-name="{{ $user->name }}" data-qr-code-url="{{ route('admin.users.qrcode', $user->id) }}" title="Lihat QR Code">
                                            <i class="bi bi-qr-code"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}" title="Edit User">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus User">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada pengguna yang cocok dengan filter.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginasi --}}
                @if ($users->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $users->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </form>
    
    {{-- Memanggil Partial untuk Modals --}}
    @include('users.partials.modals', ['users' => $users, 'roles' => $roles, 'devisis' => $devisis])
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Logika untuk checkbox dan aksi massal
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.user-checkbox');
    const bulkActionControls = document.getElementById('bulkActionControls');
    const bulkActionSelect = document.getElementById('bulkActionSelect');
    const bulkDevisiSelect = document.getElementById('bulkDevisiSelect');
    const selectedCountSpan = document.getElementById('selectedCount');

    function updateBulkActionUI() {
        const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
        selectedCountSpan.textContent = `${checkedCount} item terpilih`;
        bulkActionControls.classList.toggle('d-none', checkedCount === 0);
        bulkActionControls.classList.toggle('d-flex', checkedCount > 0);
    }

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        updateBulkActionUI();
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionUI);
    });

    bulkActionSelect.addEventListener('change', function() {
        bulkDevisiSelect.classList.toggle('d-none', this.value !== 'change_devisi');
    });

    // Logika untuk Modal QR Code
    const qrCodeModal = document.getElementById('qrCodeModal');
    if(qrCodeModal) {
        qrCodeModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const userName = button.getAttribute('data-user-name');
            const qrCodeUrl = button.getAttribute('data-qr-code-url');
            
            const modalTitle = qrCodeModal.querySelector('.modal-title');
            const userNameInModal = qrCodeModal.querySelector('#userNameInModal');
            const qrCodeContainer = qrCodeModal.querySelector('#qrCodeContainer');

            modalTitle.textContent = 'QR Code untuk ' + userName;
            userNameInModal.textContent = userName;
            qrCodeContainer.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';

            fetch(qrCodeUrl)
                .then(response => response.text())
                .then(svg => {
                    qrCodeContainer.innerHTML = svg;
                })
                .catch(err => {
                    qrCodeContainer.innerHTML = '<p class="text-danger">Gagal memuat QR Code.</p>';
                });
        });
    }
});
</script>
@endpush