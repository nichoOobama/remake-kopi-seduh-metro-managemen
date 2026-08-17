<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'CoffeePaste')</title>

    {{-- Bootstrap 5 via CDN agar tidak perlu build frontend --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="bg-light">

{{-- Navbar menyesuaikan role user --}}
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
            <i class="bi bi-cup-hot-fill"></i> CoffeePaste
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>

                @if (auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.gerobak.*') ? 'active' : '' }}" href="{{ route('admin.gerobak.index') }}">
                            <i class="bi bi-cart-check"></i> Monitoring Gerobak
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.produk.*') ? 'active' : '' }}" href="{{ route('admin.produk.index') }}">
                            <i class="bi bi-box-seam"></i> Produk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <i class="bi bi-people"></i> Pengguna
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.log.*') ? 'active' : '' }}" href="{{ route('admin.log.index') }}">
                            <i class="bi bi-clock-history"></i> Log Aktivitas
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('komisi.*') ? 'active' : '' }}" href="{{ route('komisi.index') }}">
                            <i class="bi bi-cash-coin"></i> Komisi &amp; Bagian Hasil
                        </a>
                    </li>
                @endif
            </ul>

            <div class="d-flex align-items-center gap-2">
                @if (! auth()->user()->isAdmin())
                    {{-- Saldo penghasilan karyawan --}}
                    <span class="badge text-bg-success">
                        <i class="bi bi-wallet2"></i> Saldo: Rp{{ number_format(auth()->user()->balance, 0, ',', '.') }}
                    </span>
                @endif

                <span class="text-white small">
                    {{ auth()->user()->name }}
                    <span class="badge text-bg-{{ auth()->user()->isAdmin() ? 'warning' : 'info' }}">
                        {{ auth()->user()->isAdmin() ? 'Admin' : 'Employee' }}
                    </span>
                </span>

                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>

<main class="container py-4">
    {{-- Pesan flash (success / error) --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Error validasi (semua, ditampilkan sekali) --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Terdapat kesalahan:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>