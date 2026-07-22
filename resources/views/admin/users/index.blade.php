@extends('layouts.admin')

@section('page_title', 'Kelola Users')

@section('content')
<style>
    .custom-card {
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        border-radius: 16px;
    }
</style>

<div class="container-fluid px-0">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert" style="background: rgba(34, 197, 94, 0.15); color: #15803d;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert" style="background: rgba(239, 68, 68, 0.15); color: #b91c1c;">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4" style="background: rgba(239, 68, 68, 0.15); color: #b91c1c;">
            <div class="fw-bold mb-1"><i class="fas fa-exclamation-circle me-1"></i> Terjadi kesalahan input:</div>
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Card Container -->
    <div class="custom-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 border-bottom pb-3">
            <div>
                <h5 class="fw-bold mb-1 text-dark"><i class="fas fa-user-gear me-2 text-primary"></i> Manajemen Pengguna Platform</h5>
                <p class="text-muted small mb-0">Kelola daftar seluruh pengguna, hak akses peran (Admin / User), edit profil, dan tambah akun baru.</p>
            </div>
            <button class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-user-plus me-2"></i> Tambah User Baru
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 50px;">#</th>
                        <th>Pengguna</th>
                        <th>Alamat Email</th>
                        <th>Role Saat Ini</th>
                        <th>Tanggal Terdaftar</th>
                        <th class="text-end pe-5">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $u)
                    <tr>
                        <td class="ps-3 text-muted fw-semibold">{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($u->name) }}&background={{ $u->isAdmin() ? '4f46e5' : '64748b' }}&color=fff" 
                                     alt="{{ $u->name }}" class="rounded-circle me-3" width="38" height="38">
                                <div>
                                    <div class="fw-bold text-dark">{{ $u->name }}</div>
                                    @if(auth()->id() === $u->id)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 10px;">(Anda)</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="text-muted">{{ $u->email }}</td>
                        <td>
                            @if($u->isAdmin())
                                <span class="badge bg-primary text-white px-3 py-2 rounded-pill">
                                    <i class="fas fa-user-shield me-1"></i> Admin
                                </span>
                            @else
                                <span class="badge bg-secondary text-white px-3 py-2 rounded-pill">
                                    <i class="fas fa-user me-1"></i> User
                                </span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $u->created_at ? $u->created_at->format('d M Y, H:i') : '-' }}</td>
                        <td class="text-end pe-3">
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}">
                                <i class="fas fa-edit me-1"></i> Edit
                            </button>

                            @if(auth()->id() !== $u->id)
                                <button class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $u->id }}">
                                    <i class="fas fa-trash me-1"></i> Hapus
                                </button>
                            @endif
                        </td>
                    </tr>

                    <!-- Modal Edit User -->
                    <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <form action="{{ route('admin.users.update', $u->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header border-bottom px-4">
                                        <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-pen me-2 text-primary"></i> Edit Pengguna: {{ $u->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small text-muted">Nama Lengkap</label>
                                            <input type="text" name="name" class="form-control" value="{{ old('name', $u->name) }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small text-muted">Alamat Email</label>
                                            <input type="email" name="email" class="form-control" value="{{ old('email', $u->email) }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small text-muted">Role Pengguna</label>
                                            <select name="role" class="form-select" {{ auth()->id() === $u->id ? 'disabled' : '' }}>
                                                <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>Regular User</option>
                                                <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Administrator</option>
                                            </select>
                                            @if(auth()->id() === $u->id)
                                                <input type="hidden" name="role" value="admin">
                                                <small class="text-muted d-block mt-1">Anda tidak dapat mengubah role akun sendiri.</small>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small text-muted">Password Baru (Opsional)</label>
                                            <input type="password" name="password" class="form-control" placeholder="Biarkan kosong jika tidak diubah">
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top px-4">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Delete User -->
                    @if(auth()->id() !== $u->id)
                    <div class="modal fade" id="deleteUserModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-header border-bottom px-4">
                                        <h5 class="modal-title fw-bold text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Hapus Pengguna</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4 text-center">
                                        <i class="fas fa-trash-alt fa-3x text-danger mb-3 opacity-75"></i>
                                        <h6 class="fw-bold text-dark">Apakah Anda yakin ingin menghapus akun '{{ $u->name }}'?</h6>
                                        <p class="text-muted small mb-0">Tindakan ini tidak dapat dibatalkan dan seluruh data pengguna akan terhapus.</p>
                                    </div>
                                    <div class="modal-footer border-top px-4">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">Ya, Hapus User</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom px-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-plus me-2 text-primary"></i> Tambah Pengguna Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Alamat Email</label>
                        <input type="email" name="email" class="form-control" placeholder="user@gmail.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Role Pengguna</label>
                        <select name="role" class="form-select" required>
                            <option value="user" selected>Regular User</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Password (Min. 6 Karakter)</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>
                <div class="modal-footer border-top px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Buat Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
