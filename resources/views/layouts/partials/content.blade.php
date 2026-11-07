@auth
    <div class="container pb-5">
        <div class="row">
            <aside class="col-md-3 col-lg-2 mb-4">
                @include('layouts.partials.sidebar')
            </aside>
            <main class="col-md-9 col-lg-10">
                @yield('content')
            </main>
        </div>
    </div>
@else
    <main>
        @yield('content')
    </main>
@endauth
