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
    @auth
        <div class="sms-app-shell" :class="{ 'sidebar-collapsed': !sidebarOpen }">
            @include('layouts.partials.sidebar')
            <div class="sms-main-column">
                @include('layouts.partials.header')
                @include('layouts.partials.content')
            </div>
        </div>
    @else
        @include('layouts.partials.content')
    @endauth
</body>
</html>
