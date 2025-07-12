@extends('layouts.admin')

@section('title', 'Riwayat Pengumuman')

@section('content')
<div class="container">
    <h4 class="mb-4">Riwayat Pengiriman Pengumuman</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($logs->count())
        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Judul Pengumuman</th>
                        <th>Nama Penerima</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Dikirim Pada</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $index => $log)
                        <tr>
                            <td>{{ $logs->firstItem() + $index }}</td>
                            <td>{{ $log->announcement->title }}</td>
                            {{-- START OF MODIFIED CODE --}}
                            <td>{{ $log->user->name ?? 'User tidak ditemukan' }}</td>
                            <td>{{ $log->user->email ?? '-' }}</td>
                            {{-- END OF MODIFIED CODE --}}
                            <td>
                                @if($log->is_sent)
                                    <span class="badge bg-success">Terkirim</span>
                                @else
                                    <span class="badge bg-danger">Gagal</span>
                                @endif
                            </td>
                            <td>{{ $log->sent_at ? \Carbon\Carbon::parse($log->sent_at)->format('d M Y, H:i') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $logs->links() }}
        </div>
    @else
        <p class="text-muted">Belum ada riwayat pengiriman pengumuman.</p>
    @endif
</div>
@endsection