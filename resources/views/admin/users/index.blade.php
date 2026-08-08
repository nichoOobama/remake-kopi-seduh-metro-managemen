@extends('layouts.app')

@section('title', 'Kelola Pengguna — CoffeePaste')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-people"></i> Kelola Pengguna</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Dashboard</a>
    </div>

    <div class="row g-4">
        {{-- Form tambah akun (admin bisa pilih role) --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold"><i class="bi bi-person-plus"></i> Tambah Akun Baru</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" minlength="8" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="employee">Employee</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <button class="btn btn-warning w-100 fw-bold"><i class="bi bi-check-lg"></i> Simpan Akun</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Daftar pengguna --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th class="text-center">Role</th>
                                    <th class="text-end">Saldo</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $u)
                                    <tr>
                                        <td class="fw-semibold">{{ $u->name }}</td>
                                        <td>{{ $u->email }}</td>
                                        <td class="text-center">
                                            <span class="badge text-bg-{{ $u->role === 'admin' ? 'warning' : 'info' }}">
                                                {{ ucfirst($u->role) }}
                                            </span>
                                        </td>
                                        <td class="text-end">Rp{{ number_format($u->balance, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            @if ($u->id === auth()->id())
                                                <span class="text-muted small">Anda</span>
                                            @else
                                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="m-0"
                                                      onsubmit="return confirm('Hapus akun {{ $u->name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pengguna.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection