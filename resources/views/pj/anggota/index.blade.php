@extends('layouts.pj')

@section('title', 'Daftar Anggota Devisi')

@section('content')
<div class="container-fluid">
    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-people-fill me-2"></i>
            Daftar Anggota Devisi: <span class="text-primary">{{ $devisi->nama_devisi }}</span>
        </h4>
        <span class="text-muted">Total: {{ $anggotas->total() }} Anggota</span>
    </div>

    {{-- Card Tabel Anggota --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th>Nama Anggota</th>
                            <th>Email</th>
                            <th>Bergabung pada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($anggotas as $anggota)
                            <tr>
                                <td>{{ $loop->iteration + $anggotas->firstItem() - 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($anggota->name) }}&background=0D6EFD&color=fff" alt="Avatar" class="rounded-circle me-3" style="width: 40px; height: 40px;">
                                        <div>
                                            <h6 class="mb-0">{{ $anggota->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $anggota->email }}</td>
                                <td>{{ $anggota->created_at->isoFormat('D MMMM YYYY') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-person-slash fs-3 d-block mb-2"></i>
                                    Belum ada anggota di devisi ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Paginasi --}}
            @if ($anggotas->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $anggotas->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection