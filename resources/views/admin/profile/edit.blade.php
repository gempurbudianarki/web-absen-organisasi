@extends('layouts.admin')

@section('title', 'Edit Profil Saya')

@section('content')
<div class="container-fluid">
    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-person-badge-fill me-2"></i>
            Profil Saya
        </h4>
    </div>
    
    {{-- Notifikasi --}}
    @if (session('status') === 'profile-updated')
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>Profil berhasil diperbarui.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if (session('status') === 'password-updated')
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>Password berhasil diperbarui.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif


    <div class="row g-4">
        {{-- Kolom Kiri untuk Informasi Profil dan Ganti Password --}}
        <div class="col-lg-8">
            {{-- Card Informasi Profil --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    @include('admin.profile.update-profile-information-form')
                </div>
            </div>

            {{-- Card Ganti Password --}}
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    @include('admin.profile.update-password-form')
                </div>
            </div>
        </div>

        {{-- Kolom Kanan untuk Aksi Berbahaya --}}
        <div class="col-lg-4">
            {{-- Card Hapus Akun --}}
            <div class="card shadow-sm border-danger">
                <div class="card-body p-4">
                     @include('admin.profile.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection