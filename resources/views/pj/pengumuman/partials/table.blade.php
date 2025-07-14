<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Judul</th>
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
                    {{-- PERBAIKAN: Kalkulasi nomor urut paginasi yang benar --}}
                    <td>{{ $pengumuman->firstItem() + $index }}</td>
                    <td>
                        <div class="fw-bold">{{ $item->judul }}</div>
                        <small class="text-muted">{{ Str::limit(strip_tags($item->isi), 70) }}</small>
                    </td>
                    <td>{{ $item->user->name }}</td>
                    <td>
                        @if($item->expires_at)
                            {{ $item->expires_at->isoFormat('D MMM YYYY, HH:mm') }}
                            @if($type == 'aktif')
                                <div class="text-muted small">({{ $item->expires_at->diffForHumans() }})</div>
                            @endif
                        @else
                            <span class="badge bg-success">Selamanya</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <form action="{{ route('pj.pengumuman.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="bi bi-bell-slash fs-3"></i>
                        <p class="mt-2 mb-0">Tidak ada data pengumuman di sini.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>