<nav class="navbar navbar-expand-md navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand sms-brand" href="{{ route('home') }}">{{ config('app.name') }}</a>
        <div class="ms-auto d-flex align-items-center gap-3 text-white">
            <span class="small">{{ auth()->user()->name }}</span>
            <a class="btn btn-sm btn-outline-light" href="{{ route('profile.show') }}">Hồ sơ cá nhân</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light">Đăng xuất</button>
            </form>
        </div>
    </div>
</nav>
