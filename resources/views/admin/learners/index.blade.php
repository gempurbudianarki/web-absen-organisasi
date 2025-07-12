@extends('layouts.admin')

@section('title', 'Manajemen Anggota')

@section('content')
<div class="container-fluid px-2">

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#3085d6',
                    timer: 3000,
                    timerProgressBar: true,
                    confirmButtonText: 'OK'
                });
            });
        </script>
    @endif
 
    <div class="sticky-top bg-white shadow-sm py-2 mb-0">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Anggota</h5>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addLearnerModal">
                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Anggota
                </button>
            </div>
        </div>
    </div>

    <div class="overflow-auto rounded-lg border border-gray-300 shadow-sm">
         <table class="table table-sm table-compact table-bordered table-hover bg-white">
            <thead class="table-light">
                <tr>
                    <th style="width: 1%;">No.</th>
                    <th class="px-3 py-2 text-left">Nama</th>
                    <th class="px-3 py-2 text-left">Email</th>
                    <th class="px-3 py-2 text-left">Tingkat</th>
                    <th class="px-3 py-2 text-left">Seksi</th>
                    <th class="px-3 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($learners as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-1">{{ $loop->iteration }}</td>
                        <td class="px-3 py-1">{{ $user->name }}</td>
                        <td class="px-3 py-1">{{ $user->email }}</td>
                        <td class="px-3 py-1">{{ $user->grade_level }}</td>
                        <td class="px-3 py-1">{{ $user->section }}</td>
                        <td class="px-3 py-1 text-center">
                            <button type="button"
                                class="btn btn-secondary btn-sm rounded-pill d-inline-flex align-items-center justify-content-center gap-1 px-3 py-1"
                                data-bs-toggle="modal"
                                data-bs-target="#editLearnerModal{{ $user->id }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <form action="{{ route('admin.learners.destroy', $user->id) }}" method="POST"
                                onsubmit="return confirm('Hapus anggota ini?')" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm rounded-pill d-inline-flex align-items-center justify-content-center gap-1 px-3 py-1">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <div class="modal fade" id="editLearnerModal{{ $user->id }}" tabindex="-1" aria-labelledby="editLearnerLabel{{ $user->id }}" aria-hidden="true">
                      <div class="modal-dialog modal-lg modal-dialog-centered">
                          <div class="modal-content border border-1 border-primary rounded-4 shadow">
                          <form action="{{ route('admin.learners.update', $user->id) }}" method="POST">
                              @csrf
                              @method('PUT')
                              <div class="modal-header py-2 px-3">
                              <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="editLearnerLabel{{ $user->id }}">
                                  <i class="bi bi-pencil-square"></i>
                                  Edit Anggota
                              </h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>

                              <div class="modal-body pt-1">
                              <div class="container-fluid">
                                  <div class="row g-3 mb-3">
                                  <div class="col-md-4">
                                      <label class="form-label">Nama Depan</label>
                                      <input type="text" name="fname" class="form-control" value="{{ $user->fname }}" required>
                                  </div>
                                  <div class="col-md-4">
                                      <label class="form-label">Nama Tengah</label>
                                      <input type="text" name="mname" class="form-control" value="{{ $user->mname }}">
                                  </div>
                                  <div class="col-md-4">
                                      <label class="form-label">Nama Belakang</label>
                                      <input type="text" name="lname" class="form-control" value="{{ $user->lname }}" required>
                                  </div>
                                  </div>

                                  <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tingkat</label>
                                        <select name="grade_level" class="form-select" required>
                                        <option disabled>Pilih Tingkat</option>
                                        @foreach(['1st Year','2nd Year','3rd Year','4th Year'] as $year)
                                            <option value="{{ $year }}" @selected($user->grade_level === $year)>{{ $year }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Seksi</label>
                                        <select name="section" class="form-select" required>
                                        <option disabled>Pilih Seksi</option>
                                        @foreach(['A','B','C','D'] as $sec)
                                            <option value="{{ $sec }}" @selected($user->section === $sec)>{{ $sec }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                  </div>
                              </div>
                              </div>

                              <div class="modal-footer d-flex justify-content-end">
                              <button type="button" class="btn btn-outline-primary rounded-pill px-4"
                                      style="background-color: transparent !important; border-color: #0d6efd; color: #0d6efd;"
                                      data-bs-dismiss="modal">
                                      Batal
                                  </button>
                              <button type="submit" class="btn btn-primary rounded-pill px-4">Update</button>
                              </div>
                          </form>
                          </div>
                      </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addLearnerModal" tabindex="-1" aria-labelledby="addLearnerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border border-1 border-primary rounded-4 shadow"  style="z-index: 1055;">
      <form action="{{ route('admin.learners.store') }}" method="POST">
        @csrf
        <div class="modal-header border-bottom-0">
            <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="addLearnerModalLabel">
                <i class="bi bi-person-plus-fill"></i>
                Tambah Anggota Baru
            </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body pt-0">
          <div class="container-fluid">

            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <label for="fname" class="form-label">Nama Depan</label>
                <input type="text" name="fname" class="form-control rounded-3" placeholder="Nama Depan" required>
              </div>

              <div class="col-md-4">
                <label for="mname" class="form-label">Nama Tengah</label>
                <input type="text" name="mname" class="form-control rounded-3" placeholder="Nama Tengah">
              </div>

              <div class="col-md-4">
                <label for="lname" class="form-label">Nama Belakang</label>
                <input type="text" name="lname" class="form-control rounded-3" placeholder="Nama Belakang" required>
              </div>
            </div>
            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control rounded-3" placeholder="Email" required>
              </div>
              <div class="col-md-4">
                <label for="grade_level" class="form-label">Tingkat</label>
                <select name="grade_level" class="form-select rounded-3" required>
                  <option selected disabled>Pilih Tingkat</option>
                  <option>1st Year</option>
                  <option>2nd Year</option>
                  <option>3rd Year</option>
                  <option>4th Year</option>
                </select>
              </div>

              <div class="col-md-4">
                <label for="section" class="form-label">Seksi</label>
                <select name="section" class="form-select rounded-3" required>
                  <option selected disabled>Pilih Seksi</option>
                  <option>A</option>
                  <option>B</option>
                  <option>C</option>
                  <option>D</option>
                </select>
              </div>
            </div>

          </div>
        </div>

        <div class="modal-footer border-top-0 d-flex justify-content-end">
            <button type="button" class="btn btn-outline-primary rounded-pill px-4"
                style="background-color: transparent !important; border-color: #0d6efd; color: #0d6efd;"
                data-bs-dismiss="modal">
                Batal
            </button>
          <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection