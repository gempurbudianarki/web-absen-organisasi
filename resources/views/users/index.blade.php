@extends('layouts.admin')

@section('title', 'Manajemen User')

@section('content')
<div class="container-fluid">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
     @if (session('emailSuccess'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Email Terkirim!',
                    text: '{{ session('emailSuccess') }}',
                });
            });
        </script>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-people-fill me-2"></i>Manajemen User</h4>
        <a href="{{ route('admin.register.form') }}" class="btn btn-primary">
            <i class="bi bi-person-plus-fill me-1"></i> Register User Baru
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form id="sendMailForm" method="POST" action="{{ route('admin.users.sendmail') }}">
                @csrf
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:1%"><input type="checkbox" id="selectAll"></th>
                                <th style="width:1%">#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Tanggal Registrasi</th>
                                <th class="text-center" style="width:120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td><input type="checkbox" name="recipients[]" value="{{ $user->id }}" class="recipient-checkbox"></td>
                                    <td>{{ $loop->iteration + $users->firstItem() - 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td><span class="badge bg-primary">{{ $user->getRoleNames()->first() ?? 'No Role' }}</span></td>
                                    <td>{{ $user->created_at->format('d M Y, H:i') }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}" title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin hapus user {{ $user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data user.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small">
                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
                    </div>
                     <div class="d-flex align-items-center gap-2">
                        <button type="submit" id="sendEmailBtn" class="btn btn-info text-white">
                            <i class="bi bi-send-fill me-1"></i> Kirim Welcome Email
                        </button>
                        {{ $users->links() }}
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
@foreach($users as $user)
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User: {{ $user->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            @foreach(Spatie\Permission\Models\Role::all() as $role)
                                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
    document.getElementById('selectAll').addEventListener('change', function () {
        document.querySelectorAll('.recipient-checkbox').forEach(cb => cb.checked = this.checked);
    });
</script>
@endpush