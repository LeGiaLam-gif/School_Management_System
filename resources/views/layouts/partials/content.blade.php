@auth
    <div class="container pb-5">
        @include('layouts.partials.alerts')
        <div class="row">
            <aside id="app-sidebar" class="col-md-3 col-lg-2 mb-4" x-show="sidebarOpen">
                @include('layouts.partials.sidebar')
            </aside>
            <main :class="sidebarOpen ? 'col-md-9 col-lg-10' : 'col-12'">
                @yield('content')
            </main>
        </div>
    </div>
@else
    <main>
        @include('layouts.partials.alerts')
        @yield('content')
    </main>
@endauth
