@extends('layouts.app')

@section('title', $page['title'].' · '.config('app.name'))

@section('content')
    <section class="sms-workspace-page">
        <div class="sms-workspace-page-heading">
            <div>
                <span class="sms-eyebrow">HEXATECH ACADEMY · {{ mb_strtoupper($roleLabel) }}</span>
                <h1>{{ $page['title'] }}</h1>
                <p>{{ $page['description'] }}</p>
            </div>
            <a class="sms-workspace-back-link" href="{{ route($dashboardRoute) }}">Về bảng điều khiển</a>
        </div>

        <article class="sms-workspace-notice">
            <span class="sms-workspace-notice-icon" aria-hidden="true">i</span>
            <div>
                <h2>Giao diện cơ bản đã sẵn sàng</h2>
                <p>
                    Trang này là khung giao diện cho {{ mb_strtolower($roleLabel) }}.
                    Các thao tác và dữ liệu của phân hệ sẽ được bổ sung sau.
                </p>
            </div>
        </article>
    </section>
@endsection
