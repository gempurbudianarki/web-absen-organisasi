@extends('layouts.pj')

@section('title', 'Manajemen Kegiatan Devisi')

@section('content')
<div class="container-fluid">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Manajemen Kegiatan Devisi: <span class="text-primary">{{ $devisi->nama_devisi }}</span></h4>
        <a href="{{ route('pj.kegiatan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle-fill me-1"></i> Buat Kegiatan Baru
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Judul Kegiatan</th>
                            <th>Jadwal</th>
                            <th>Tempat</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kegiatans as $kegiatan)
                            <tr>
                                <td>{{ $kegiatan->judul }}</td>
                                <td>{{ \Carbon\Carbon::parse($kegiatan->waktu_mulai)->format('d M Y, H:i') }}</td>
                                <td>{{ $kegiatan->tempat }}</td>
                                <td class="text-center">
                                    <a href="{{ route('pj.kegiatan.edit', $kegiatan->id) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil-fill"></i> Edit</a>
                                    
                                    <form action="{{ route('pj.kegiatan.destroy', $kegiatan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kegiatan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash-fill"></i> Hapus</button>
                                    </form>

                                    <a href="{{ route('pj.absensi.show', $kegiatan->id) }}" class="btn btn-sm btn-info text-white"><i class="bi bi-clipboard2-check-fill"></i> Absensi</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Devisi Anda belum memiliki kegiatan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                {{ $kegiatans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection