@extends('admin.layouts.app')

@section('title', 'Approval Karyawan')

@section('content')
<div class="p-6">

    <h1 class="text-2xl font-bold mb-6">Approval Karyawan</h1>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if($users->isEmpty())
        <div class="bg-orange-50 p-4 rounded text-orange-700">
            Tidak ada karyawan yang menunggu persetujuan.
        </div>
    @else
    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Tanggal Daftar</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="border-t">
                    <td class="px-4 py-3">{{ $user->nama }}</td>
                    <td class="px-4 py-3">{{ $user->email }}</td>
                    <td class="px-4 py-3">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-center space-x-2">

                        <!-- ACC -->
                        <form action="{{ route('admin.user.approve', $user->id) }}"
                              method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <button class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                                ACC
                            </button>
                        </form>

                        <!-- TOLAK -->
                        <form action="{{ route('admin.user.reject', $user->id) }}"
                              method="POST" class="inline"
                              onsubmit="return confirm('Tolak pendaftaran ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                Tolak
                            </button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection
