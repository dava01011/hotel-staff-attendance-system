<!DOCTYPE html>
<html lang="id">
<head>
    @include('karyawan.partials.head')
    <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">

</head>
<body style="margin: 0; padding: 0;">
        @include('karyawan.partials.header')
                @include('notifikasi.toast')


    <div class="content" style="width: 100%; height: 100vh; overflow-y: auto;">
        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>
