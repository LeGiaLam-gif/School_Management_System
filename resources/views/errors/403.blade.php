<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Không có quyền truy cập</title>
    @vite('resources/css/app.css')
</head>
<body class="sms-body">
    <main class="d-flex min-vh-100 align-items-center justify-content-center p-3">
        <section class="sms-workspace-card d-flex w-100 flex-column align-items-center justify-content-center text-center"
                 style="max-width: 420px;"
                 aria-labelledby="forbidden-title">
            <p class="mb-2 fs-3 fw-bold" style="color: var(--sms-teal-dark);">
                403
            </p>

            <h1 id="forbidden-title" class="h5 mb-4">
                Bạn không có quyền truy cập trang này
            </h1>

            <a href="{{ route('home') }}" class="btn btn-primary px-4"
               style="--bs-btn-bg: var(--sms-teal-dark);
                      --bs-btn-border-color: var(--sms-teal-dark);
                      --bs-btn-hover-bg: #195548;
                      --bs-btn-hover-border-color: #195548;
                      --bs-btn-active-bg: #195548;
                      --bs-btn-active-border-color: #195548;
                      --bs-btn-focus-shadow-rgb: 34, 107, 92;">
                Về trang chủ
            </a>
        </section>
    </main>
</body>
</html>