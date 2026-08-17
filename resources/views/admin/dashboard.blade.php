@extends('layouts.app')

@section('title', 'Dashboard Admin — CoffeePaste')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-speedometer2"></i> Dashboard Admin — Monitoring</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary"><i class="bi bi-person-plus"></i> Kelola Pengguna</a>
            <a href="{{ route('admin.produk.create') }}" class="btn btn-warning fw-bold"><i class="bi bi-plus-lg"></i> Tambah Produk</a>
        </div>
    </div>

    {{-- Kartu statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-box-seam fs-3 text-primary"></i>
                <div class="fs-4 fw-bold">{{ $stats['total_produk'] }}</div>
                <div class="text-muted small">Total Produk</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-boxes fs-3 text-info"></i>
                <div class="fs-4 fw-bold">{{ $stats['total_stok'] }}</div>
                <div class="text-muted small">Total Stok Gudang</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-people fs-3 text-secondary"></i>
                <div class="fs-4 fw-bold">{{ $stats['total_karyawan'] }}</div>
                <div class="text-muted small">Karyawan</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-cart fs-3 text-warning"></i>
                <div class="fs-4 fw-bold">{{ $stats['gerobak_aktif'] }}</div>
                <div class="text-muted small">Gerobak Aktif</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-arrow-return-left fs-3 text-secondary"></i>
                <div class="fs-4 fw-bold">{{ $stats['gerobak_dikembalikan'] }}</div>
                <div class="text-muted small">Gerobak Dikembalikan</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-hourglass-split fs-3 text-secondary"></i>
                <div class="fs-4 fw-bold">Rp{{ number_format($stats['komisi_pending'], 0, ',', '.') }}</div>
                <div class="text-muted small">Komisi Menunggu Dibayar</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-cash-coin fs-3 text-success"></i>
                <div class="fs-4 fw-bold">Rp{{ number_format($stats['komisi_dibayar'], 0, ',', '.') }}</div>
                <div class="text-muted small">Komisi Sudah Dibayar</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-graph-up fs-3 text-warning"></i>
                <div class="fs-4 fw-bold">Rp{{ number_format($stats['total_penjualan'], 0, ',', '.') }}</div>
                <div class="text-muted small">Total Penjualan</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-graph-up-arrow fs-3 text-primary"></i>
                <div class="fs-4 fw-bold">Rp{{ number_format($stats['penjualan_7hari'], 0, ',', '.') }}</div>
                <div class="text-muted small">Penjualan 7 Hari</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-cash-stack fs-3 text-success"></i>
                <div class="fs-4 fw-bold">Rp{{ number_format($stats['upah_7hari'], 0, ',', '.') }}</div>
                <div class="text-muted small">Bagi Hasil 7 Hari</div>
            </div>
        </div>
    </div>

    {{-- Line chart: hasil penjualan per karyawan 7 hari terakhir --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-bold"><i class="bi bi-bar-chart-line"></i> Statistik Penjualan per Karyawan (7 Hari Terakhir)</span>
            <select id="filterChart" class="form-select form-select-sm" style="width: 220px;">
                <option value="all">Semua karyawan</option>
                @foreach ($chartKaryawan as $k)
                    <option value="{{ $k->id }}">{{ $k->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="card-body">
            <canvas id="chartPenjualan" height="110"></canvas>
            <div class="small text-muted mt-2">
                <i class="bi bi-info-circle"></i>
                Penjualan dihitung dari gerobak yang dikembalikan pada hari tersebut ({{ $persenBagiHasil * 100 }}% jadi bagian karyawan).
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Komisi terbaru --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold"><i class="bi bi-receipt"></i> Bagian Hasil Terbaru</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Karyawan</th>
                                    <th class="text-end">Penjualan</th>
                                    <th class="text-end">Keuntungan</th>
                                    <th class="text-end">Upah (20%)</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($komisiTerbaru as $k)
                                    <tr>
                                        <td>{{ $k->user->name }}</td>
                                        <td class="text-end">Rp{{ number_format($k->total_penjualan, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp{{ number_format($k->total_untung, 0, ',', '.') }}</td>
                                        <td class="text-end fw-semibold">Rp{{ number_format($k->upah_20persen, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <span class="badge text-bg-{{ $k->status === 'paid' ? 'success' : 'secondary' }}">
                                                {{ $k->status === 'paid' ? 'Diambil' : 'Menunggu' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data bagian hasil.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info recall --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white fw-bold"><i class="bi bi-arrow-return-left"></i> Fitur Retur Terintegrasi</div>
                <div class="card-body small">
                    <p class="mb-2">
                        Saat karyawan <strong>mengembalikan gerobak</strong>, sistem otomatis:
                    </p>
                    <ul class="mb-0">
                        <li>menghitung barang terjual = diambil − sisa retur;</li>
                        <li>mengembalikan sisa barang ke stok gudang;</li>
                        <li>menghitung keuntungan dan upah karyawan (20%).</li>
                    </ul>
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold"><i class="bi bi-lightbulb"></i> Tombol Cepat</div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('admin.gerobak.index') }}" class="btn btn-outline-primary"><i class="bi bi-cart-check"></i> Monitoring Gerobak</a>
                    <a href="{{ route('admin.log.index') }}" class="btn btn-outline-secondary"><i class="bi bi-clock-history"></i> Log Aktivitas</a>
                    <a href="{{ route('admin.produk.index') }}" class="btn btn-outline-primary"><i class="bi bi-box-seam"></i> CRUD Produk</a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="bi bi-people"></i> Kelola Pengguna</a>
                    <a href="{{ route('admin.produk.create') }}" class="btn btn-warning"><i class="bi bi-plus-lg"></i> Tambah Produk Baru</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    const chartLabels = @json($chartLabels);
    const chartSeries = @json($chartSeries);
    const palette = [
        '#0d6efd', '#dc3545', '#198754', '#fd7e14', '#6f42c1',
        '#20c997', '#d63384', '#0dcaf0', '#ffc107', '#6610f2',
    ];

    const allDatasets = chartSeries.map((s, i) => ({
        label: s.name,
        data: s.data,
        borderColor: palette[i % palette.length],
        backgroundColor: palette[i % palette.length],
        tension: 0.35,
        borderWidth: 2,
        pointRadius: 4,
    }));

    const chartPenjualan = new Chart(document.getElementById('chartPenjualan'), {
        type: 'line',
        data: { labels: chartLabels, datasets: allDatasets },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ' ' + ctx.dataset.label + ': Rp' +
                            Number(ctx.parsed.y).toLocaleString('id-ID'),
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: (v) => 'Rp' + Number(v).toLocaleString('id-ID') },
                },
            },
        },
    });

    document.getElementById('filterChart').addEventListener('change', function () {
        const userId = this.value;
        chartPenjualan.data.datasets = userId === 'all'
            ? allDatasets
            : allDatasets.filter((_, i) => chartSeries[i].id == userId);
        chartPenjualan.update();
    });
</script>
@endpush