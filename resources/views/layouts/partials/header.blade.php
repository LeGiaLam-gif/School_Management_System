<nav class="navbar navbar-expand-md navbar-dark bg-primary mb-4">
    <div class="container sms-header-content">
        <div class="d-flex align-items-center gap-2">
            <button
                class="btn btn-outline-light sms-sidebar-toggle"
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
            <a class="navbar-brand sms-brand d-flex align-items-center gap-2 mb-0" href="{{ route('home') }}">
                <img src="{{ asset('images/logo.png') }}" alt="" class="sms-logo">
                <span>Hexa Tech Academy</span>
            </a>
        </div>
        <div class="ms-auto d-flex align-items-center gap-3 text-white sms-header-actions">
            <span class="small sms-header-name">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light">Đăng xuất</button>
            </form>
        </div>
    </div>
</nav>
