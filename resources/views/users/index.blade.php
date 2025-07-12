@extends('layouts.admin')

@section('title', 'Manajemen User')

@section('content')
<div class="container">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Filter Users</h5>
            <form method="GET" action="{{ route('users.index') }}" class="row g-3 align-items-end">
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
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <form action="{{ route('users.bulk_action') }}" method="POST" id="bulkActionForm" onsubmit="return confirm('Apakah Anda yakin ingin melakukan aksi ini pada item terpilih?');">
        @csrf
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar User</h5>
                <a href="{{ route('admin.register.form') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-person-plus-fill"></i> Register User Baru
                </a>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3" id="bulkActionControls" style="display: none;">
                    <select name="action" id="bulkActionSelect" class="form-select form-select-sm" style="width: 200px;">
                        <option value="">Pilih Aksi Massal</option>
                        <option value="change_devisi">Pindahkan Devisi</option>
                        <option value="delete">Hapus Pengguna</option>
                    </select>
                    <select name="devisi_id" id="bulkDevisiSelect" class="form-select form-select-sm" style="width: 200px; display: none;">
                        <option value="">Pilih Devisi Tujuan</option>
                        @foreach($devisis as $id => $nama_devisi)
                            <option value="{{ $id }}">{{ $nama_devisi }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-info">Terapkan</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-compact table-bordered table-hover bg-white">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 1%;"><input type="checkbox" id="selectAll"></th>
                                <th>No.</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Devisi</th>
                                <th class="text-center" style="width:180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td><input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox"></td>
                                    <td>{{ $loop->iteration + $users->firstItem() - 1 }}</td>
                                    <td><a href="{{ route('users.show', $user->id) }}">{{ $user->name }}</a></td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-info text-dark">{{ $user->getRoleNames()->first() ?? 'No Role' }}</span>
                                    </td>
                                    <td>{{ $user->devisi->nama_devisi ?? '-' }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-info me-1" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#qrCodeModal"
                                                data-user-name="{{ $user->name }}"
                                                data-qr-code-url="{{ route('users.qrcode', $user->id) }}">
                                            <i class="bi bi-qr-code"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-secondary me-1" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center">Tidak ada user yang cocok dengan filter.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </form>
    
    @include('users.partials.modals', ['users' => $users, 'roles' => $roles, 'devisis' => $devisis])
</div>
@endsection

@push('scripts')
    @if(session('success'))
        <script>
            Swal.fire({ icon: 'success', title: 'Success!', text: '{{ session('success') }}', timer: 2500, showConfirmButton: false });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.user-checkbox');
            const bulkActionControls = document.getElementById('bulkActionControls');
            const bulkActionSelect = document.getElementById('bulkActionSelect');
            const bulkDevisiSelect = document.getElementById('bulkDevisiSelect');

            function toggleBulkActions() {
                const anyChecked = Array.from(checkboxes).some(c => c.checked);
                bulkActionControls.style.display = anyChecked ? 'flex' : 'none';
            }

            selectAll.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                toggleBulkActions();
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', toggleBulkActions);
            });

            bulkActionSelect.addEventListener('change', function() {
                bulkDevisiSelect.style.display = this.value === 'change_devisi' ? 'block' : 'none';
            });
        });

        const qrCodeModal = document.getElementById('qrCodeModal');
        qrCodeModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const userName = button.getAttribute('data-user-name');
            const qrCodeUrl = button.getAttribute('data-qr-code-url');
            
            const modalTitle = qrCodeModal.querySelector('.modal-title');
            const userNameInModal = qrCodeModal.querySelector('#userNameInModal');
            const qrCodeContainer = qrCodeModal.querySelector('#qrCodeContainer');

            modalTitle.textContent = 'QR Code for ' + userName;
            userNameInModal.textContent = userName;
            
            qrCodeContainer.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';

            fetch(qrCodeUrl)
                .then(response => response.text())
                .then(svg => {
                    qrCodeContainer.innerHTML = svg;
                });
        });
    </script>
@endpush