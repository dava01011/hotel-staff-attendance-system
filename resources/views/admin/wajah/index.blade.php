@extends('admin.layouts.app')

@section('title', 'Data Karyawan')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Data Karyawan</h4>
            <small class="text-muted">Manajemen data karyawan</small>
        </div>

        {{-- <button class="btn btn-primary btn-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#tambahKaryawan">
            <i class="fas fa-plus me-2"></i>Tambah Data
        </button> --}}
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        {{-- <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>latitude</th>
                        <th>longitude</th>
                        <th>Status</th> --}}
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($karyawan as $k)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $k->nip }}</td>
                            <td>{{ $k->user->nama }}</td>
                            <td>
                                <a href="{{ route('admin.wajah.capture', $k->id) }}" class="btn btn-sm btn-primary">
                                    Daftar Wajah
                                </a>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
        @foreach ($karyawan as $item)
            {{-- @include('admin.karyawan.edit')
            @include('admin.karyawan.delete') --}}
        @endforeach
    </div>
    {{-- @include('admin.karyawan.create') --}}


@endsection
