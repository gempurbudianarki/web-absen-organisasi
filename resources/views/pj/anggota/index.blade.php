@extends('layouts.pj')

@section('title', 'Daftar Anggota Devisi')

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

    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-people-fill me-2"></i>
                Anggota Devisi: <span class="text-primary">{{ $devisi->nama_devisi }}</span>
            </h4>
            <p class="text-muted mb-0">Total: {{ $anggotas->total() }} Anggota</p>
        </div>
        {{-- Tombol Register Anggota Baru --}}
        <a href="{{ route('pj.anggota.register') }}" class="btn btn-primary">
            <i class="bi bi-person-plus-fill me-1"></i> Register Anggota Baru
        </a>
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
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($anggotas as $anggota)
                            <tr>
                                <td>{{ $loop->iteration + $anggotas->firstItem() - 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($anggota->name) }}&background=0D6EFD&color=fff&size=40" alt="Avatar" class="rounded-circle me-3">
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $anggota->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $anggota->email }}</td>
                                <td>{{ $anggota->created_at->isoFormat('D MMMM YYYY') }}</td>
                                <td class="text-center">
                                    {{-- Tombol Keluarkan Anggota --}}
                                    <form action="{{ route('pj.anggota.remove', $anggota->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin mengeluarkan {{ $anggota->name }} dari devisi ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-box-arrow-left me-1"></i> Keluarkan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-person-slash fs-2 d-block mb-2"></i>
                                    Belum ada anggota di devisi ini. <br>
                                    Gunakan tombol "Register Anggota Baru" untuk memulai.
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