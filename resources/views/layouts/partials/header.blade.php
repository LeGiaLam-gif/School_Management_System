<header class="sms-topbar">
    <button
        class="sms-sidebar-toggle sms-sidebar-toggle-mobile"
        type="button"
        @click="sidebarOpen = !sidebarOpen"
        :aria-expanded="sidebarOpen.toString()"
        aria-controls="app-sidebar"
        aria-label="Ẩn hoặc hiện thanh điều hướng"
    >
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
            <path d="M3 6h18M3 12h18M3 18h18" />
        </svg>
    </button>
    <div class="sms-header-actions">
        <div class="sms-user-summary">
            <span class="sms-user-avatar" aria-hidden="true">
                <svg viewBox="0 0 24 24" focusable="false">
                    <circle cx="12" cy="8" r="4" />
                    <path d="M4 21a8 8 0 0 1 16 0" />
                </svg>
            </span>
            <span class="sms-user-name">{{ auth()->user()->name }}</span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sms-logout-button">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M10 17l5-5-5-5M15 12H3" />
                    <path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7" />
                </svg>
                <span>Đăng xuất</span>
            </button>
        </form>
    </div>
</header>
