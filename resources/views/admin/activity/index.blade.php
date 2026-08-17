@extends('layouts.app')

@section('title', 'Log Aktivitas — CoffeePaste')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-clock-history"></i> Log Aktivitas</h1>
        <div>
            <span class="badge text-bg-secondary"><i class="bi bi-lock"></i> Read-only — admin tidak dapat mengubah/hapus log</span>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Cari User / Deskripsi</label>
                    <input type="text" name="q" value="{{ $cari }}" class="form-control form-control-sm" placeholder="Nama karyawan, kata kunci...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Aksi</label>
                    <select name="aksi" class="form-select form-select-sm">
                        <option value="">Semua aksi</option>
                        @foreach ($daftarAksi as $a)
                            <option value="{{ $a }}" @selected($aksi === $a)>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Dari tanggal</label>
                    <input type="date" name="dari" value="{{ $dari }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Sampai tanggal</label>
                    <input type="date" name="sampai" value="{{ $sampai }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-sm btn-primary"><i class="bi bi-funnel"></i> Filter</button>
                    <a href="{{ route('admin.log.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel log (read-only) --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 160px;">Waktu</th>
                            <th>User</th>
                            <th style="width: 150px;">Aksi</th>
                            <th>Deskripsi</th>
                            <th class="text-end" style="width: 150px;">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td class="text-nowrap small">{{ $log->created_at?->format('d M Y H:i:s') }}</td>
                                <td>
                                    <span class="fw-semibold">{{ $log->user->name }}</span>
                                    <span class="badge text-bg-{{ $log->user->isAdmin() ? 'warning' : 'info' }} ms-1">
                                        {{ $log->user->isAdmin() ? 'Admin' : 'Employee' }}
                                    </span>
                                </td>
                                <td><span class="badge text-bg-secondary">{{ $log->action }}</span></td>
                                <td>{{ $log->description }}</td>
                                <td class="text-end small text-muted">{{ $log->ip_address ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada log aktivitas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($logs->hasPages())
            <div class="card-footer bg-white">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection