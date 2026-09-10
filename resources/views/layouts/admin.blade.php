<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Godong Ijo Console</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/admin.css', 'resources/js/admin.js', 'resources/js/admin-pages.js'])
    
    <style>
    /* Notification System Styles */
    .notification-wrapper {
        position: relative;
    }
    
    .notification-btn {
        position: relative;
    }
    
    .notification-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        background: #EF4444;
        color: white;
        font-size: 10px;
        font-weight: 600;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
        animation: notificationPulse 2s ease-in-out infinite;
    }
    
    @keyframes notificationPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    
    .notification-dropdown {
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        width: 380px;
        max-height: 500px;
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12), 0 4px 8px rgba(0, 0, 0, 0.08);
        display: none;
        flex-direction: column;
        z-index: 1000;
        overflow: hidden;
    }
    
    .notification-dropdown.show {
        display: flex;
        animation: dropdownFadeIn 0.2s ease;
    }
    
    @keyframes dropdownFadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .notification-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 18px;
        border-bottom: 1px solid #F3F4F6;
    }
    
    .notification-header h3 {
        font-size: 15px;
        font-weight: 600;
        color: #111827;
        margin: 0;
    }
    
    .mark-all-read-btn {
        font-size: 12px;
        color: #6B7280;
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 6px;
        transition: all 0.15s ease;
    }
    
    .mark-all-read-btn:hover {
        color: #111827;
        background: #F3F4F6;
    }
    
    .notification-list {
        flex: 1;
        overflow-y: auto;
        max-height: 400px;
    }
    
    .notification-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        gap: 12px;
        color: #6B7280;
        font-size: 13px;
    }
    
    .spinner {
        width: 24px;
        height: 24px;
        border: 3px solid #F3F4F6;
        border-top-color: #111827;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .notification-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        gap: 8px;
        color: #9CA3AF;
    }
    
    .notification-empty svg {
        width: 48px;
        height: 48px;
        color: #D1D5DB;
        margin-bottom: 8px;
    }
    
    .notification-empty-title {
        font-size: 14px;
        font-weight: 500;
        color: #6B7280;
    }
    
    .notification-empty-text {
        font-size: 12.5px;
        color: #9CA3AF;
    }
    
    .notification-item {
        display: flex;
        gap: 12px;
        padding: 14px 18px;
        border-bottom: 1px solid #F9FAFB;
        cursor: pointer;
        transition: background 0.15s ease;
        position: relative;
    }
    
    .notification-item:hover {
        background: #F9FAFB;
    }
    
    .notification-item:last-child {
        border-bottom: none;
    }
    
    .notification-item.unread {
        background: #F0F9FF;
    }
    
    .notification-item.unread:hover {
        background: #E0F2FE;
    }
    
    .notification-item.unread::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: #3B82F6;
    }
    
    .notification-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .notification-icon svg {
        width: 18px;
        height: 18px;
        stroke-width: 2;
    }
    
    .notification-content {
        flex: 1;
        min-width: 0;
    }
    
    .notification-title {
        font-size: 13px;
        font-weight: 500;
        color: #111827;
        margin: 0 0 3px;
        line-height: 1.4;
    }
    
    .notification-message {
        font-size: 12.5px;
        color: #6B7280;
        margin: 0 0 6px;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .notification-time {
        font-size: 11.5px;
        color: #9CA3AF;
        font-family: 'IBM Plex Mono', monospace;
    }
    
    .notification-footer {
        padding: 12px 18px;
        border-top: 1px solid #F3F4F6;
        text-align: center;
    }
    
    .notification-footer a {
        font-size: 12.5px;
        color: #6B7280;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.15s ease;
    }
    
    .notification-footer a:hover {
        color: #111827;
    }
    
    /* Custom scrollbar for notification list */
    .notification-list::-webkit-scrollbar {
        width: 6px;
    }
    
    .notification-list::-webkit-scrollbar-track {
        background: #F9FAFB;
    }
    
    .notification-list::-webkit-scrollbar-thumb {
        background: #D1D5DB;
        border-radius: 3px;
    }
    
    .notification-list::-webkit-scrollbar-thumb:hover {
        background: #9CA3AF;
    }
    </style>
    
    @stack('styles')
</head>
<body>
<div class="app">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="brand">
            <svg class="brand-mark" viewBox="0 0 24 24" fill="none">
                <path d="M12 21c-4.5-.6-7.5-3.8-7.5-9C4.5 6.8 8 3.5 13 3c-.3 5-1.3 8-4 10.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                <path d="M12 21V9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
            <div class="brand-text">
                <div class="brand-name">Godong Ijo</div>
                <div class="brand-tag">Operations Console</div>
            </div>
        </div>
        <div class="env-row">
            <span class="env-dot"></span> Sistem Aktif
        </div>

        <ul class="nav">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="3" width="7" height="9" rx="1"/>
                        <rect x="14" y="3" width="7" height="5" rx="1"/>
                        <rect x="14" y="12" width="7" height="9" rx="1"/>
                        <rect x="3" y="16" width="7" height="5" rx="1"/>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.bookings.index') }}" class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="4" width="18" height="17" rx="1.5"/>
                        <path d="M3 9h18M8 3v3M16 3v3"/>
                    </svg>
                    Booking
                    <span class="nav-count" id="navBookingCount">{{ $pendingBookingsCount ?? '00' }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.paket-wisata.index') }}" class="nav-link {{ request()->routeIs('admin.paket-wisata.*') ? 'active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M21 8 12 3 3 8l9 5 9-5Z"/>
                        <path d="M3 8v8l9 5 9-5V8"/>
                    </svg>
                    Paket Wisata
                </a>
            </li>
            <li>
                <a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="9" cy="8" r="3"/>
                        <path d="M3.5 20c1-3.5 3.3-5.3 5.5-5.3s4.5 1.8 5.5 5.3"/>
                        <circle cx="17.5" cy="8.8" r="2.2"/>
                        <path d="M15.7 14.9c1.9.3 3.4 1.9 4.2 4.5"/>
                    </svg>
                    Pelanggan
                </a>
            </li>
        </ul>

        <div class="nav-section">
            <div class="nav-section-title">Analitik</div>
            <ul class="nav">
                <li>
                    <a href="{{ route('admin.reports') }}" class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M3 3v18h18"/>
                            <path d="M7 15v3M12 10v8M17 6v12"/>
                        </svg>
                        Laporan
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.activity') }}" class="nav-link {{ request()->routeIs('admin.activity') ? 'active' : '' }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M12 7v5l3.5 2"/>
                        </svg>
                        Riwayat Aktivitas
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Sistem</div>
            <ul class="nav">
                <li>
                    <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V21a2 2 0 1 1-4 0v-.2a1.7 1.7 0 0 0-1-1.5 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H3a2 2 0 1 1 0-4h.2a1.7 1.7 0 0 0 1.5-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.9.3H9a1.7 1.7 0 0 0 1-1.6V3a2 2 0 1 1 4 0v.2a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.9V9a1.7 1.7 0 0 0 1.6 1H21a2 2 0 1 1 0 4h-.2a1.7 1.7 0 0 0-1.5 1Z"/>
                        </svg>
                        Pengaturan
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-bottom">
            <div class="sidebar-user">
                <div class="sidebar-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'AD', 0, 2)) }}</div>
                <div>
                    <div class="sidebar-user-name">{{ Auth::user()->name ?? 'Administrator' }}</div>
                    <div class="sidebar-user-role">{{ Auth::user()->role ?? 'Admin' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="logout-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <path d="M16 17l5-5-5-5"/>
                        <path d="M21 12H9"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="main">
        <!-- TOPBAR -->
        <header class="topbar">
            <div class="crumb">
                <span class="dim">Godong Ijo</span>
                <span class="dim">/</span>
                <span class="cur">@yield('page-title', 'Dashboard')</span>
            </div>
            <div class="search">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
                <input type="text" id="globalSearch" placeholder="Cari booking, pelanggan…">
                <span class="kbd">⌘K</span>
            </div>
            <div class="topbar-right">
                <!-- Notification Dropdown -->
                <div class="notification-wrapper">
                    <button class="icon-btn notification-btn" id="notificationBtn" aria-label="Notifikasi">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.7 21a2 2 0 0 1-3.4 0"/>
                        </svg>
                        <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
                    </button>
                    
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">
                            <h3>Notifikasi</h3>
                            <button class="mark-all-read-btn" id="markAllReadBtn">
                                Tandai semua dibaca
                            </button>
                        </div>
                        
                        <div class="notification-list" id="notificationList">
                            <!-- Loading state -->
                            <div class="notification-loading">
                                <div class="spinner"></div>
                                <span>Memuat notifikasi...</span>
                            </div>
                        </div>
                        
                        <div class="notification-footer">
                            <a href="{{ route('admin.activity') }}">Lihat semua aktivitas</a>
                        </div>
                    </div>
                </div>
                
                <div class="divider-v"></div>
                <div class="profile-btn" title="{{ Auth::user()->name ?? 'Administrator' }}">
                    <div class="avatar-sm">{{ strtoupper(substr(Auth::user()->name ?? 'AD', 0, 2)) }}</div>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--ink-45)">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="content">
            @if(session('success'))
                <div class="alert alert-success" id="flashSuccess">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error" id="flashError">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<!-- Toast Container -->
<div id="toastContainer"></div>

<script>
// Notification System JavaScript
(function() {
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationBadge = document.getElementById('notificationBadge');
    const notificationList = document.getElementById('notificationList');
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    
    let isDropdownOpen = false;
    let notifications = [];
    
    // CSRF Token for POST requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Toggle dropdown
    notificationBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        isDropdownOpen = !isDropdownOpen;
        
        if (isDropdownOpen) {
            notificationDropdown.classList.add('show');
            loadNotifications();
        } else {
            notificationDropdown.classList.remove('show');
        }
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (isDropdownOpen && !notificationDropdown.contains(e.target)) {
            notificationDropdown.classList.remove('show');
            isDropdownOpen = false;
        }
    });
    
    // Prevent dropdown close when clicking inside
    notificationDropdown.addEventListener('click', function(e) {
        e.stopPropagation();
    });
    
    // Load notifications
    async function loadNotifications() {
        try {
            showLoading();
            
            const response = await fetch('{{ route('admin.notifications.index') }}?limit=15', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });
            
            if (!response.ok) throw new Error('Failed to fetch notifications');
            
            const result = await response.json();
            notifications = result.data || [];
            
            renderNotifications();
        } catch (error) {
            console.error('Error loading notifications:', error);
            showError();
        }
    }
    
    // Render notifications
    function renderNotifications() {
        if (notifications.length === 0) {
            showEmpty();
            return;
        }
        
        const html = notifications.map(notification => {
            const unreadClass = notification.is_read ? '' : 'unread';
            const iconColor = notification.color;
            const iconBg = hexToRgba(iconColor, 0.1);
            
            return `
                <div class="notification-item ${unreadClass}" data-id="${notification.id}" data-booking-id="${notification.booking_id || ''}">
                    <div class="notification-icon" style="background: ${iconBg}; color: ${iconColor};">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            ${notification.icon}
                        </svg>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${escapeHtml(notification.title)}</div>
                        <div class="notification-message">${escapeHtml(notification.message)}</div>
                        <div class="notification-time">${escapeHtml(notification.time_ago)}</div>
                    </div>
                </div>
            `;
        }).join('');
        
        notificationList.innerHTML = html;
        
        // Add click handlers
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function() {
                const notificationId = this.dataset.id;
                const bookingId = this.dataset.bookingId;
                const isUnread = this.classList.contains('unread');
                
                if (isUnread) {
                    markAsRead(notificationId);
                }
                
                // Redirect to booking detail if booking_id exists
                if (bookingId) {
                    window.location.href = `/admin/bookings/${bookingId}`;
                }
            });
        });
    }
    
    // Show loading state
    function showLoading() {
        notificationList.innerHTML = `
            <div class="notification-loading">
                <div class="spinner"></div>
                <span>Memuat notifikasi...</span>
            </div>
        `;
    }
    
    // Show empty state
    function showEmpty() {
        notificationList.innerHTML = `
            <div class="notification-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.7 21a2 2 0 0 1-3.4 0"/>
                </svg>
                <div class="notification-empty-title">Tidak ada notifikasi</div>
                <div class="notification-empty-text">Anda akan melihat notifikasi di sini</div>
            </div>
        `;
    }
    
    // Show error state
    function showError() {
        notificationList.innerHTML = `
            <div class="notification-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 8v4M12 16h.01"/>
                </svg>
                <div class="notification-empty-title">Gagal memuat</div>
                <div class="notification-empty-text">Terjadi kesalahan saat memuat notifikasi</div>
            </div>
        `;
    }
    
    // Mark notification as read
    async function markAsRead(notificationId) {
        try {
            const response = await fetch(`/admin/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                }
            });
            
            if (!response.ok) throw new Error('Failed to mark as read');
            
            // Update UI
            const item = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
            if (item) {
                item.classList.remove('unread');
            }
            
            // Update badge
            updateBadgeCount();
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }
    
    // Mark all as read
    markAllReadBtn.addEventListener('click', async function(e) {
        e.stopPropagation();
        
        if (notifications.filter(n => !n.is_read).length === 0) {
            return;
        }
        
        try {
            const response = await fetch('{{ route('admin.notifications.mark-all-read') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                }
            });
            
            if (!response.ok) throw new Error('Failed to mark all as read');
            
            // Update UI
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
            });
            
            // Update badge
            updateBadgeCount();
        } catch (error) {
            console.error('Error marking all as read:', error);
        }
    });
    
    // Update badge count
    async function updateBadgeCount() {
        try {
            const response = await fetch('{{ route('admin.notifications.unread-count') }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });
            
            if (!response.ok) throw new Error('Failed to fetch unread count');
            
            const result = await response.json();
            const count = result.count || 0;
            
            if (count > 0) {
                notificationBadge.textContent = count > 99 ? '99+' : count;
                notificationBadge.style.display = 'flex';
            } else {
                notificationBadge.style.display = 'none';
            }
        } catch (error) {
            console.error('Error updating badge count:', error);
        }
    }
    
    // Utility: Convert hex to rgba
    function hexToRgba(hex, alpha) {
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }
    
    // Utility: Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Initial badge count load
    updateBadgeCount();
    
    // Auto-refresh badge count every 30 seconds
    setInterval(updateBadgeCount, 30000);
})();

// Global Search Keyboard Shortcut (⌘K or Ctrl+K)
(function() {
    const globalSearchInput = document.getElementById('globalSearch');
    
    if (!globalSearchInput) return;
    
    // Handle keyboard shortcut: Cmd+K (Mac) or Ctrl+K (Windows/Linux)
    document.addEventListener('keydown', function(e) {
        // Check for Cmd+K (Mac) or Ctrl+K (Windows/Linux)
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault(); // Prevent browser's default Ctrl+K behavior
            globalSearchInput.focus();
            globalSearchInput.select();
        }
        
        // Also handle Escape to unfocus
        if (e.key === 'Escape' && document.activeElement === globalSearchInput) {
            globalSearchInput.blur();
        }
    });
    
    // Handle search submission
    globalSearchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const searchTerm = this.value.trim();
            
            if (searchTerm === '') {
                return;
            }
            
            // Redirect to bookings page with search query
            window.location.href = '{{ route('admin.bookings.index') }}?search=' + encodeURIComponent(searchTerm);
        }
    });
    
    // Optional: Add visual feedback when focused
    globalSearchInput.addEventListener('focus', function() {
        this.parentElement.style.borderColor = 'var(--brand)';
    });
    
    globalSearchInput.addEventListener('blur', function() {
        this.parentElement.style.borderColor = '';
    });
})();
</script>

<x-ai-chatbot audience="admin" endpoint="{{ route('admin.chatbot.message') }}" />

@stack('scripts')

</body>
</html>
