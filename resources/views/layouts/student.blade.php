@extends('layouts.app')

@section('title')
    @yield('title', 'Cổng Sinh Viên · ' . config('app.name'))
@endsection

@section('content')
    @yield('content')
@endsection
