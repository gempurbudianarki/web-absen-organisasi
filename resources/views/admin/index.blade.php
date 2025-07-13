@extends('layouts.admin')

@section('title', 'Manajemen Pengumuman')

@push('styles')
{{-- Tambahkan style untuk Flatpickr jika belum ada di layout utama --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-megaphone-fill me-2"></i>
            Manajemen Pengumuman
        </h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPengumumanModal">
            <i class="bi bi-plus-circle-fill me-1"></i> Buat Pengumuman Baru
        </button>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white p-0 border-0">
            {{-- Navigasi Tab --}}
            <ul class="nav nav-tabs nav-fill" id="pengumumanTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="aktif-tab" data-bs-toggle="tab" data-bs-target="#aktif-tab-pane" type="button" role="tab" aria-controls="aktif-tab-pane" aria-selected="true">
                        <i class="bi bi-broadcast me-1"></i> Pengumuman Aktif 
                        <span class="badge bg-success rounded-pill">{{ $pengumumanAktif->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat-tab-pane" type="button" role="tab" aria-controls="riwayat-tab-pane" aria-selected="false">
                        <i class="bi bi-archive-fill me-1"></i> Riwayat Pengumuman 
                        <span class="badge bg-secondary rounded-pill">{{ $pengumumanRiwayat->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="pengumumanTabContent">
                {{-- Konten Tab Aktif --}}
                <div class="tab-pane fade show active" id="aktif-tab-pane" role="tabpanel" aria-labelledby="aktif-tab">
                    @include('admin.pengumuman.partials.table', ['pengumuman' => $pengumumanAktif, 'type' => 'aktif'])
                </div>
                {{-- Konten Tab Riwayat --}}
                <div class="tab-pane fade" id="riwayat-tab-pane" role="tabpanel" aria-labelledby="riwayat-tab">
                    @include('admin.pengumuman.partials.table', ['pengumuman' => $pengumumanRiwayat, 'type' => 'riwayat'])
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.pengumuman.partials.create-modal')
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inisialisasi Flatpickr untuk input tanggal
        flatpickr("#publish_at", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today",
        });
        flatpickr("#expires_at", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today",
        });

        // Logika untuk menampilkan/menyembunyikan pilihan devisi
        const targetSelect = document.getElementById('target');
        const devisiSelectDiv = document.getElementById('devisiSelectDiv');
        targetSelect.addEventListener('change', function() {
            if (this.value === 'devisi') {
                devisiSelectDiv.style.display = 'block';
            } else {
                devisiSelectDiv.style.display = 'none';
            }
        });
    });
</script>
@endpush