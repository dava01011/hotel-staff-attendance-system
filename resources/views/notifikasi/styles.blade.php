{{-- resources/views/notifikasi/styles.blade.php --}}

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
    }

    body {
        margin: 0;
        padding: 0;
        background: #ffffff;
        overflow: hidden;
    }

    .notifikasi-wrapper {
        margin-top: 70px;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        flex-direction: column;
        background: #ffffff;
    }

    /* Header Bar */
    .header-bar {
        background: #2d2d2e;
        color: white;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        flex-shrink: 0;
    }

    .header-title {
        font-size: 18px;
        font-weight: 500;
    }

    /* Filter Tabs */
    .filter-tabs {
        display: flex;
        gap: 0;
        background: #f8f9fa;
        padding: 0;
        border-bottom: 2px solid #e9ecef;
        flex-shrink: 0;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .tab-btn {
        flex: 0 0 auto;
        min-width: max-content;
        padding: 14px 20px;
        background: transparent;
        border: none;
        color: #6c757d;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
        white-space: nowrap;
    }

    .tab-btn.active {
        color: #212529;
        font-weight: 600;
    }

    .tab-btn.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 2px;
        background: #212529;
    }

    /* Notifications List */
    .notifications-list {
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding: 0;
        background: #ffffff;
    }

    .notification-item {
        border-bottom: 1px solid #f0f0f0;
    }

    .notification-card {
        padding: 16px 20px;
        display: flex;
        gap: 16px;
        align-items: flex-start;
        background: white;
        transition: background 0.2s;
        cursor: pointer;
    }

    .notification-card:active {
        background: #fafafa;
    }

    .notification-item.unread .notification-card {
        background: #f8fbff;
    }

    /* Icon */
    .notification-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        flex-shrink: 0;
    }

    /* Content */
    .notification-content {
        flex: 1;
        min-width: 0;
    }

    .notification-header {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 6px;
        align-items: flex-start;
    }

    .notification-title {
        font-size: 16px;
        font-weight: 600;
        color: #212529;
        flex: 1;
        word-break: break-word;
    }

    .notification-time {
        font-size: 13px;
        color: #adb5bd;
        white-space: nowrap;
        flex-shrink: 0;
        margin-left: 8px;
    }

    .notification-message {
        font-size: 14px;
        color: #6c757d;
        line-height: 1.5;
        word-break: break-word;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        background: #f8f9fa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #adb5bd;
        font-size: 36px;
    }

    .empty-state h3 {
        font-size: 16px;
        color: #495057;
        margin-bottom: 6px;
        font-weight: 600;
    }

    .empty-state p {
        font-size: 13px;
        color: #adb5bd;
    }

    /* Pagination */
    .pagination-wrapper {
        padding: 16px 20px;
        background: white;
        border-top: 1px solid #e9ecef;
        flex-shrink: 0;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 6px;
        margin: 0;
        padding: 0;
        list-style: none;
        flex-wrap: wrap;
    }

    .pagination a,
    .pagination span {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        font-size: 13px;
        color: #495057;
        text-decoration: none;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        transition: all 0.2s;
    }

    .pagination a:hover {
        background: #2d2d2e;
        color: white;
    }

    .pagination .active span {
        background: #2d2d2e;
        color: white;
    }

    .pagination .disabled span {
        opacity: 0.4;
        cursor: not-allowed;
    }

    /* Tablet & Mobile Responsiveness */
    @media (max-width: 768px) {
        .header-bar {
            padding: 16px 16px;
        }

        .header-title {
            font-size: 16px;
        }

        .filter-tabs {
            overflow-x: auto;
        }

        .tab-btn {
            padding: 12px 16px;
            font-size: 14px;
        }

        .notification-card {
            padding: 14px 16px;
            gap: 12px;
        }

        .notification-icon {
            width: 44px;
            height: 44px;
            font-size: 18px;
        }

        .notification-title {
            font-size: 15px;
        }

        .notification-time {
            font-size: 12px;
        }

        .notification-message {
            font-size: 13px;
        }

        .empty-state {
            padding: 60px 16px;
        }

        .empty-icon {
            width: 64px;
            height: 64px;
            font-size: 28px;
        }
    }

    @media (max-width: 480px) {
        .header-bar {
            padding: 14px 12px;
        }

        .header-title {
            font-size: 15px;
        }

        .filter-tabs {
            overflow-x: auto;
            scroll-behavior: smooth;
        }

        .tab-btn {
            padding: 10px 14px;
            font-size: 13px;
        }

        .notification-card {
            padding: 12px 12px;
            gap: 10px;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            font-size: 16px;
            min-width: 40px;
        }

        .notification-header {
            gap: 8px;
        }

        .notification-title {
            font-size: 14px;
        }

        .notification-time {
            font-size: 11px;
        }

        .notification-message {
            font-size: 12px;
        }

        .empty-state {
            padding: 50px 12px;
        }

        .empty-icon {
            width: 56px;
            height: 56px;
            font-size: 24px;
            margin-bottom: 16px;
        }

        .empty-state h3 {
            font-size: 14px;
        }

        .empty-state p {
            font-size: 12px;
        }

        .pagination-wrapper {
            padding: 12px 16px;
        }

        .pagination {
            gap: 4px;
        }

        .pagination a,
        .pagination span {
            width: 32px;
            height: 32px;
            font-size: 12px;
        }
    }

    /* Extra small devices */
    @media (max-width: 360px) {
        .header-title {
            font-size: 14px;
        }

        .tab-btn {
            padding: 8px 12px;
            font-size: 12px;
        }

        .notification-card {
            padding: 10px 10px;
        }

        .notification-icon {
            width: 36px;
            height: 36px;
            font-size: 14px;
        }

        .notification-title {
            font-size: 13px;
        }

        .notification-message {
            font-size: 11px;
        }
    }
</style>
