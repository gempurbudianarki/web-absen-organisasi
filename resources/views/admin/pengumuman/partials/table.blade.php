<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Judul</th>
                <th scope="col">Target</th>
                <th scope="col">Pembuat</th>
                @if($type == 'aktif')
                <th scope="col">Akan Berakhir Pada</th>
                @else
                <th scope="col">Berakhir Pada</th>
                @endif
                <th scope="col" class="text-end">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pengumuman as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="fw-bold">{{ $item->judul }}</div>
                        <small class="text-muted">{{ Str::limit(strip_tags($item->konten), 50) }}</small>
                    </td>
                    <td>
                        @if($item->target == 'semua')
                            <span class="badge bg-info">Semua Anggota</span>
                        @else
                            <span class="badge bg-warning text-dark">{{ $item->devisi->nama_devisi ?? 'N/A' }}</span>
                        @endif
                    </td>
                    <td>{{ $item->user->name }}</td>
                    <td>
                        @if($item->expires_at)
                            {{ $item->expires_at->format('d M Y, H:i') }}
                            @if($type == 'aktif')
                                <div class="text-muted small">({{ $item->expires_at->diffForHumans() }})</div>
                            @endif
                        @else
                            <span class="badge bg-success">Selamanya</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <form action="{{ route('admin.pengumuman.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        Tidak ada data pengumuman.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>