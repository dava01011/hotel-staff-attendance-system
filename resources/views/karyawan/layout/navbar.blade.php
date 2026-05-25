<li class="nav-item dropdown">
    <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown">
        <i class="fas fa-bell"></i>

        @if($notifCount > 0)
            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                {{ $notifCount }}
            </span>
        @endif
    </a>

    <ul class="dropdown-menu dropdown-menu-end shadow" style="width: 320px;">
        <li class="dropdown-header fw-bold">Notifikasi</li>

        @forelse($notifUnread as $notif)
            <li>
                <a class="dropdown-item small" href="{{ route('notifikasi.index') }}">
                    <strong>{{ $notif->judul }}</strong><br>
                    <span class="text-muted">{{ $notif->pesan }}</span>
                </a>
            </li>
        @empty
            <li class="dropdown-item text-muted text-center">
                Tidak ada notifikasi
            </li>
        @endforelse

        @if($notifCount > 0)
            <li><hr class="dropdown-divider"></li>
            <li class="text-center">
                <a href="{{ route('notifikasi.readAll') }}"
                   onclick="event.preventDefault(); document.getElementById('readAll').submit();"
                   class="small">
                    Tandai semua dibaca
                </a>
            </li>
        @endif
    </ul>

    <form id="readAll" action="{{ route('notifikasi.readAll') }}" method="POST" class="d-none">
        @csrf
    </form>
</li>
