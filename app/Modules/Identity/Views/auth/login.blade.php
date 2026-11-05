@extends('layouts.app')

@section('title', 'Đăng nhập · '.config('app.name'))

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-12 col-sm-10 col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h4 mb-1">{{ config('app.name') }}</h1>
                    <p class="text-muted small mb-4">Đăng nhập bằng email hoặc mã số</p>

                    <form method="POST" action="{{ route('login.attempt') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="login" class="form-label">Email hoặc mã số</label>
                            <input id="login" name="login" type="text" value="{{ old('login') }}"
                                   class="form-control @error('login') is-invalid @enderror"
                                   autocomplete="username" autofocus required>
                            @error('login')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input id="password" name="password" type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   autocomplete="current-password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                            <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
