<!DOCTYPE html>
<html lang="id">
<head>
    @include('karyawan.partials.head')
    <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">

    <style>
        body.fullscreen-mode {
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        body.fullscreen-mode .content {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow-y: auto;
            z-index: 9999;
            background: white;
        }

        body.fullscreen-mode header,
        body.fullscreen-mode .bottom-nav {
            display: none;
        }

        #toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
}

.toast-custom {
    min-width: 280px;
    padding: 14px 18px;
    border-radius: 12px;
    color: #fff;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 8px 20px rgba(0,0,0,.15);
    animation: slideIn .3s ease;
}

.toast-success { background: #22c55e; }
.toast-error   { background: #ef4444; }

@keyframes slideIn {
    from { opacity: 0; transform: translateX(30px); }
    to   { opacity: 1; transform: translateX(0); }
}

    </style>
</head>
<div id="toast-container"></div>

<body>
    @include('karyawan.partials.header')


    <div class="content">
        @yield('content')
        @include('notifikasi.toast')

    </div>

    @include('karyawan.partials.bottom-nav')
    @stack('scripts')
</body>
</html>
<script>
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast-custom toast-${type}`;
    toast.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
        <span>${message}</span>
    `;

    document.getElementById('toast-container').appendChild(toast);

    setTimeout(() => toast.remove(), 3000);
}
</script>

