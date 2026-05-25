<style>
    /* .header {
    display: flex;
    align-items: center;
    padding: 14px 16px;
    background: #c62828;
    color: #fff;
}

.page-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0 auto;
}

.back-btn {
    color: #fff;
    font-size: 18px;
    text-decoration: none;
} */

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    body {
        background: #f5f5f5;
        min-height: 100vh;
        padding-bottom: 70px;
    }

    .header {
        background: #2d2d2e;
        color: white;
        padding: 20px;
        position: sticky;
        top: 0;
        z-index: 100;
        text-align: center;
    }

    .header-logo img {
        height: 64px;
        object-fit: contain;
        margin-bottom: 10px;
    }

    .header-info {
        margin-bottom: 10px;
    }

    .header-name {
        font-size: 1rem;
        font-weight: 600;
    }

    .header-date {
        font-size: 0.85rem;
        opacity: 0.9;
    }

    .header-status {
        margin-top: 10px;
    }

    .status-badge {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        display: inline-block;
    }

    .logout-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        padding: 8px 12px;
        border-radius: 8px;
        cursor: pointer;
    }

    .back-btn {
        position: absolute;
        top: 20px;
        left: 20px;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        padding: 8px 12px;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .content {
        padding: 20px;
        max-width: 600px;
        margin: 0 auto;
    }

    .card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .card h2 {
        font-size: 1.1rem;
        margin-bottom: 15px;
        color: #333;
    }

    .alert {
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        font-size: 0.9rem;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border-left: 3px solid #28a745;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border-left: 3px solid #dc3545;
    }

    .alert-warning {
        background: #fff3cd;
        color: #856404;
        border-left: 3px solid #ffc107;
    }

    .btn {
        width: 100%;
        padding: 15px;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        margin-bottom: 10px;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }

    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-primary {
        background: #FF6B35;
        color: white;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn i {
        margin-right: 8px;
    }

    .status-box {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-top: 15px;
    }

    .status-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
    }

    .status-row span:first-child {
        color: #666;
    }

    .status-row span:last-child {
        font-weight: 600;
    }

    .table-wrap {
        overflow-x: auto;
        margin-top: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }

    th {
        background: #f8f9fa;
        padding: 12px 8px;
        text-align: left;
        font-weight: 600;
        color: #555;
    }

    td {
        padding: 12px 8px;
        border-bottom: 1px solid #eee;
    }

    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .badge-success {
        background: #d4edda;
        color: #155724;
    }

    .badge-danger {
        background: #f8d7da;
        color: #721c24;
    }

    .badge-warning {
        background: #fff3cd;
        color: #856404;
    }

    .bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        display: flex;
        border-top: 1px solid #ddd;
        padding: 10px 0;
        z-index: 100;
    }

    .nav-item {
        flex: 1;
        text-align: center;
        color: #666;
        text-decoration: none;
        padding: 5px;
    }

    .nav-item.active {
        color: #FF6B35;
    }

    .nav-item i {
        display: block;
        font-size: 1.5rem;
        margin-bottom: 3px;
    }

    .nav-item span {
        font-size: 0.75rem;
    }

    .empty {
        text-align: center;
        padding: 30px;
        color: #999;
    }

    .empty i {
        font-size: 2.5rem;
        margin-bottom: 10px;
        opacity: 0.3;
    }

    .step-indicator {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 20px;
        gap: 10px;
    }

    .step-item {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #ddd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #666;
    }

    .step-item.active {
        background: #28a745;
        color: white;
    }

    .step-item.completed {
        background: #28a745;
        color: white;
    }

    .step-line {
        width: 50px;
        height: 2px;
        background: #ddd;
    }

    .step-line.active {
        background: #28a745;
    }

    .video-container {
        position: relative;
        background: #000;
        border-radius: 8px;
        margin: 15px 0;
        overflow: hidden;
    }

    #video {
        width: 100%;
        display: block;
        transform: scaleX(-1);
    }

    #overlay {
        position: absolute;
        top: 0;
        left: 0;
        pointer-events: none;
        /* transform: scaleX(-1); */
    }

    .detection-status {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 8px;
        margin: 10px 0;
        font-size: 0.9rem;
    }

    .status-item {
        display: flex;
        justify-content: space-between;
        padding: 5px 0;
    }

    .location-info {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin: 15px 0;
    }

    .location-info p {
        margin: 5px 0;
        font-size: 0.9rem;
    }

    .distance-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 10px;
    }

    .distance-valid {
        background: #d4edda;
        color: #155724;
    }

    .distance-invalid {
        background: #f8d7da;
        color: #721c24;
    }

    #map {
        height: 250px;
        border-radius: 8px;
        margin: 15px 0;
    }


.video-container {
    position: relative;
    width: 100%;
    max-width: 420px;
    margin: auto;
}

video, canvas {
    width: 100%;
    border-radius: 12px;
}

.video-overlay {
    position: absolute;
    inset: 0;
    border-radius: 12px;
    background: radial-gradient(
        circle at center,
        transparent 40%,
        rgba(0,0,0,.55) 100%
    );
    pointer-events: none;
}
</style>
