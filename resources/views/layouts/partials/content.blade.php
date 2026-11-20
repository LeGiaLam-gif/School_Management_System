@if (auth()->check() && ! ($hideAppShell ?? false))
    <main class="sms-page-content" :class="{ 'sidebar-collapsed-content pl-12': !sidebarOpen }">
        @include('layouts.partials.alerts')
        @yield('content')
    </main>
@else
    <main class="sms-guest-content">
        @include('layouts.partials.alerts')
        @yield('content')
    </main>
@endif
