@extends('layouts.admin')

@section('title', 'Input Absensi Manual')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-pencil-square me-2"></i>
            Input Absensi Manual
        </h4>
        <a href="{{ route('admin.absensi.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke Laporan
        </a>
    </div>

    {{-- Manual Input Form Card --}}
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.absensi.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    {{-- START OF MODIFIED CODE --}}
                    {{-- User Selection --}}
                    <div class="col-md-6">
                        <label for="learner_id" class="form-label fw-bold">Pilih Anggota</label>
                        <select name="learner_id" id="learner_id" class="form-select" required>
                            <option value="" disabled selected>-- Cari dan Pilih Anggota --</option>
                            @foreach ($learners as $user) {{-- Variable is now $user, but we call it $learners from controller for now --}}
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- END OF MODIFIED CODE --}}

                    {{-- Date Selection --}}
                    <div class="col-md-6">
                        <label for="date" class="form-label fw-bold">Tanggal Absensi</label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}" required>
                    </div>

                    {{-- Morning Session --}}
                    <div class="col-md-6">
                        <fieldset class="border p-3 rounded">
                            <legend class="fs-6 fw-bold w-auto px-2">Sesi Pagi</legend>
                            <div class="mb-3">
                                <label for="am_in" class="form-label">Jam Masuk (AM IN)</label>
                                <input type="time" class="form-control" id="am_in" name="am_in">
                            </div>
                            <div>
                                <label for="am_out" class="form-label">Jam Pulang (AM OUT)</label>
                                <input type="time" class="form-control" id="am_out" name="am_out">
                            </div>
                        </fieldset>
                    </div>

                    {{-- Afternoon Session --}}
                    <div class="col-md-6">
                        <fieldset class="border p-3 rounded">
                            <legend class="fs-6 fw-bold w-auto px-2">Sesi Siang/Sore</legend>
                            <div class="mb-3">
                                <label for="pm_in" class="form-label">Jam Masuk (PM IN)</label>
                                <input type="time" class="form-control" id="pm_in" name="pm_in">
                            </div>
                            <div>
                                <label for="pm_out" class="form-label">Jam Pulang (PM OUT)</label>
                                <input type="time" class="form-control" id="pm_out" name="pm_out">
                            </div>
                        </fieldset>
                    </div>

                    {{-- Submit Button --}}
                    <div class="col-12 text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save-fill me-1"></i>
                            Simpan Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection