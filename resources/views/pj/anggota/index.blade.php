@extends('layouts.pj')

@section('title', 'Daftar Anggota Devisi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-people-fill me-2"></i>
            Daftar Anggota Devisi: <span class="text-primary">{{ $devisi->nama_devisi }}</span>
        </h4>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th>Nama Anggota</th>
                            <th>Email</th>
                            <th>Bergabung pada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($anggotas as $index => $anggota)
                            <tr>
                                <td>{{ $anggotas->firstItem() + $index }}</td>
                                <td>{{ $anggota->name }}</td>
                                <td>{{ $anggota->email }}</td>
                                <td>{{ $anggota->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Belum ada anggota di devisi ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                {{ $anggotas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection