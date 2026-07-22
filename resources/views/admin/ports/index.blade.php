@extends('layouts.admin')

@section('page_title', 'Dataset Pelabuhan')

@section('content')
<style>
    .custom-card {
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        border-radius: 16px;
    }
    
    /* --- CUSTOM PAGINATION STYLE --- */
    .custom-pagination p.text-muted {
        display: none !important; 
    }
    .custom-pagination .justify-content-sm-between {
        justify-content: center !important; 
    }
    .custom-pagination .pagination {
        margin-bottom: 0;
        gap: 4px; 
    }
    .custom-pagination .page-item .page-link {
        border-radius: 8px !important; 
        padding: 8px 16px;
        font-weight: 600;
        font-size: 14px;
        color: #040405;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .custom-pagination .page-item.active .page-link {
        background-color: #03fbff;
        border-color: #03fbff;
        color: white;
        box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);
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

    <div class="custom-card p-4 mb-4">
        <!-- HEADER DENGAN FORM SEARCH -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 border-bottom pb-3">
            <div>
                <h5 class="fw-bold mb-1 text-dark"><i class="fas fa-ship me-2 text-info"></i> Kelola Dataset Pelabuhan Global</h5>
                <p class="text-muted small mb-0">Tambah lokasi pelabuhan baru, ubah koordinat lintang/bujur, atau hapus data pelabuhan.</p>
            </div>

            <!-- Bagian Tombol & Search Bar -->
            <div class="d-flex flex-column flex-sm-row gap-2 align-items-stretch align-items-sm-center">
                <!-- Form Search -->
                <form action="{{ route('admin.ports.index') }}" method="GET" class="d-flex gap-2">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control rounded-start-pill px-3" placeholder="Cari pelabuhan..." value="{{ $search ?? '' }}" style="font-size: 14px;">
                        @if(request('search'))
                            <a href="{{ route('admin.ports.index') }}" class="btn btn-outline-secondary px-3" title="Reset Pencarian">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                        <button class="btn btn-info text-white rounded-end-pill px-3" type="submit" style="font-size: 14px;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <!-- Tombol Tambah Pelabuhan -->
                <button class="btn btn-info text-white rounded-pill px-4 fw-semibold shadow-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#addPortModal" style="font-size: 14px;">
                    <i class="fas fa-plus me-2"></i> Tambah Pelabuhan
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 50px;"></th>
                        <th>Nama Pelabuhan</th>
                        <th>Negara</th>
                        <th>Latitude (Lintang)</th>
                        <th>Longitude (Bujur)</th>
                        <th class="text-end pe-3">Aksi CRUD</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ports as $index => $p)
                    <tr>
                        <td>{{ $ports->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="fw-bold text-dark"><i class="fas fa-anchor me-2 text-info"></i>{{ $p->name }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                <i class="fas fa-flag text-primary me-1"></i> {{ $p->country ? $p->country->name : 'N/A' }}
                            </span>
                        </td>
                        <td class="text-muted">{{ $p->latitude ?? '-' }}</td>
                        <td class="text-muted">{{ $p->longitude ?? '-' }}</td>
                        <td class="text-end pe-3">
                            <button class="btn btn-sm btn-outline-info rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#editPortModal{{ $p->id }}">
                                <i class="fas fa-edit me-1"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#deletePortModal{{ $p->id }}">
                                <i class="fas fa-trash me-1"></i> Hapus
                            </button>
                        </td>
                    </tr>

                    <!-- Modal Edit Port -->
                    <div class="modal fade" id="editPortModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <form action="{{ route('admin.ports.update', $p->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header border-bottom px-4">
                                        <h5 class="modal-title fw-bold text-dark"><i class="fas fa-pen-to-square me-2 text-info"></i> Edit Pelabuhan: {{ $p->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small text-muted">Nama Pelabuhan</label>
                                            <input type="text" name="name" class="form-control" value="{{ old('name', $p->name) }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small text-muted">Negara Lokasi</label>
                                            <select name="country_id" class="form-select" required>
                                                @foreach($countries as $c)
                                                    <option value="{{ $c->id }}" {{ $p->country_id == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <label class="form-label fw-semibold small text-muted">Latitude</label>
                                                <input type="number" step="any" name="latitude" class="form-control" value="{{ old('latitude', $p->latitude) }}" placeholder="-6.10000">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-semibold small text-muted">Longitude</label>
                                                <input type="number" step="any" name="longitude" class="form-control" value="{{ old('longitude', $p->longitude) }}" placeholder="106.88000">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top px-4">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-info text-white rounded-pill px-4 fw-semibold">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Delete Port -->
                    <div class="modal fade" id="deletePortModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <form action="{{ route('admin.ports.destroy', $p->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-header border-bottom px-4">
                                        <h5 class="modal-title fw-bold text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Hapus Pelabuhan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4 text-center">
                                        <i class="fas fa-trash-alt fa-3x text-danger mb-3 opacity-75"></i>
                                        <h6 class="fw-bold text-dark">Hapus pelabuhan '{{ $p->name }}'?</h6>
                                        <p class="text-muted small mb-0">Tindakan ini akan menghapus data pelabuhan dari dataset peta.</p>
                                    </div>
                                    <div class="modal-footer border-top px-4">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">Ya, Hapus Pelabuhan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-search fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0">Tidak ada data pelabuhan yang ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginasi Custom -->
        <div class="custom-pagination mt-4 mb-2 pb-2 w-100 d-flex justify-content-center">
            {{ $ports->links() }}
        </div>
        
    </div>
</div>

<!-- Modal Tambah Port -->
<div class="modal fade" id="addPortModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('admin.ports.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom px-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-anchor me-2 text-info"></i> Tambah Pelabuhan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Nama Pelabuhan</label>
                        <input type="text" name="name" class="form-control" placeholder="Port of Tanjung Priok" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Negara Lokasi</label>
                        <select name="country_id" class="form-select" required>
                            <option value="">Pilih Negara...</option>
                            @foreach($countries as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-muted">Latitude</label>
                            <input type="number" step="any" name="latitude" class="form-control" placeholder="-6.10000">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-muted">Longitude</label>
                            <input type="number" step="any" name="longitude" class="form-control" placeholder="106.88000">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white rounded-pill px-4 fw-semibold">Tambah Pelabuhan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection