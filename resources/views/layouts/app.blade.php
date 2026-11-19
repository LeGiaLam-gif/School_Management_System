<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="sms-body" x-data="{ sidebarOpen: window.innerWidth >= 768 }">
    @if (auth()->check() && ! ($hideAppShell ?? false))
        <div class="sms-app-shell" :class="{ 'sidebar-collapsed': !sidebarOpen }">
            {{-- Nút tab ghim cố định ở mép trái màn hình khi Sidebar ẩn --}}
            <button
                type="button"
                id="sms-sidebar-reopen-btn"
                class="sms-sidebar-reopen-tab fixed top-5 left-0 z-50 rounded-r-xl"
                x-show="!sidebarOpen"
                x-cloak
                @click="sidebarOpen = true"
                aria-label="Mở thanh điều hướng"
                :aria-expanded="sidebarOpen.toString()"
                aria-controls="app-sidebar"
                title="Mở thanh điều hướng"
            >
                <span class="sms-sidebar-reopen-bar" aria-hidden="true"></span>
                <svg class="sms-sidebar-reopen-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            @include('layouts.partials.sidebar')
            <div class="sms-main-column">
                @include('layouts.partials.header')
                @include('layouts.partials.content')
            </div>
        </div>
    @else
        @include('layouts.partials.content')
    @endif
</body>
</html>
