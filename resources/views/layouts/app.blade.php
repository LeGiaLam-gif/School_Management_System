<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @auth
        <nav class="navbar navbar-expand-md navbar-dark bg-primary mb-4">
            <div class="container">
                <a class="navbar-brand sms-brand" href="{{ route('home') }}">{{ config('app.name') }}</a>
                <div class="ms-auto d-flex align-items-center gap-3 text-white">
                    <span class="small">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light">Đăng xuất</button>
                    </form>
                </div>
            </div>
        </nav>
    @endauth

    @auth
        <main class="container pb-5">
            @yield('content')
        </main>
    @else
        <main>
            @yield('content')
        </main>
    @endauth
</body>
</html>
