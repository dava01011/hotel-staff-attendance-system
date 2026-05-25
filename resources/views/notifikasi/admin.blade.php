{{-- resources/views/notifikasi/admin.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="notifikasi-wrapper">
    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <button class="tab-btn active" data-filter="all">All</button>
        <button class="tab-btn" data-filter="absensi">Absensi</button>
        <button class="tab-btn" data-filter="gaji">Gaji</button>
        <button class="tab-btn" data-filter="cuti">Cuti</button>
        <button class="tab-btn" data-filter="sistem">Sistem</button>
    </div>

    <!-- Notifications List -->
    <div class="notifications-list">
        @forelse($notifikasi as $n)
        @php
            $iconClass = 'fa-bell';
            $iconBg = '#6c757d';

            switch($n->type) {
                case 'cuti':
                    $iconClass = 'fa-calendar-alt';
                    $iconBg = '#3b82f6';
                    break;
                case 'absensi':
                    $iconClass = 'fa-clipboard-check';
                    $iconBg = '#06b6d4';
                    break;
                case 'gaji':
                    $iconClass = 'fa-money-bill-wave';
                    $iconBg = '#10b981';
                    break;
            }
        @endphp

        <div class="notification-item {{ !$n->is_read ? 'unread' : '' }}" data-type="{{ $n->type }}">
            <div class="notification-card" onclick="markAsRead({{ $n->id }})">
                <div class="notification-icon" style="background: {{ $iconBg }};">
                    <i class="fas {{ $iconClass }}"></i>
                </div>

                <div class="notification-content">
                    <div class="notification-header">
                        <h3 class="notification-title">{{ $n->judul }}</h3>
                        <span class="notification-time">{{ $n->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="notification-message">{{ $n->pesan }}</p>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-inbox"></i>
            </div>
            <h3>Tidak ada notifikasi</h3>
            <p>Semua notifikasi akan muncul di sini</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifikasi->hasPages())
    <div class="pagination-wrapper">
        {{ $notifikasi->links() }}
    </div>
    @endif
</div>

@include('notifikasi.styles')
@include('notifikasi.scripts')
@endsection
