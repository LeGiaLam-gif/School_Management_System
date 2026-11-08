@auth
    <main class="sms-page-content">
        @include('layouts.partials.alerts')
        @yield('content')
    </main>
@else
    <main class="sms-guest-content">
        @include('layouts.partials.alerts')
        @yield('content')
    </main>
@endauth
