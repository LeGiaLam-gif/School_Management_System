@auth
    <main class="container pb-5">
        @yield('content')
    </main>
@else
    <main>
        @yield('content')
    </main>
@endauth
