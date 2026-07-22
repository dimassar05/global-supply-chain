@extends('layouts.admin')

@section('page_title', 'Kelola Artikel')

@section('content')
<style>
    /* Mengadopsi desain card dari halaman User */
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

    <div class="custom-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 border-bottom pb-3">
            <div>
                <h5 class="fw-bold mb-1 text-dark"><i class="fas fa-newspaper me-2 text-success"></i> Pengelolaan Artikel</h5>
                <p class="text-muted small mb-0">Kelola publikasi artikel berita dan analisis dengan formulir sederhana.</p>
            </div>
            <button class="btn btn-success rounded-pill px-4 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#addArticleModal">
                <i class="fas fa-plus me-2"></i> Tambah Artikel
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 50px;"></th>
                        <th>Judul Artikel</th>
                        <th>Isi Ringkas Artikel</th>
                        <th>Penulis</th>
                        <th>Tanggal Simpan</th>
                        <th class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($articles->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted fw-semibold">
                                <i class="fas fa-file-circle-xmark fa-2x mb-2 d-block text-secondary"></i> Belum ada artikel disimpan. Klik tombol "Tambah Artikel" untuk membuat artikel baru.
                            </td>
                        </tr>
                    @else
                        @foreach($articles as $index => $a)
                        <tr>
                            <td class="ps-3 text-muted fw-semibold">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark text-truncate" style="max-width: 250px;">{{ $a->title }}</div>
                            </td>
                            <td>
                                <div class="text-muted small text-truncate" style="max-width: 350px;">{{ $a->content }}</div>
                            </td>
                            <td class="text-muted small">{{ $a->author ? $a->author->name : 'Admin' }}</td>
                            <td class="text-muted small">{{ $a->created_at ? $a->created_at->format('d M Y, H:i') : '-' }}</td>
                            <td class="text-end pe-3">
                                <button class="btn btn-sm btn-outline-success rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#editArticleModal{{ $a->id }}">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#deleteArticleModal{{ $a->id }}">
                                    <i class="fas fa-trash me-1"></i> Hapus
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Edit Article (Sederhana: Judul & Isi) -->
                        <div class="modal fade" id="editArticleModal{{ $a->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('admin.articles.update', $a->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header border-bottom px-4">
                                            <h5 class="modal-title fw-bold text-dark"><i class="fas fa-file-pen me-2 text-success"></i> Edit Artikel</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <!-- Kotak 1: Judul Artikel -->
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark">Judul Artikel</label>
                                                <input type="text" name="title" class="form-control" value="{{ old('title', $a->title) }}" placeholder="Masukkan judul artikel..." required>
                                            </div>
                                            <!-- Kotak 2: Isi Artikel -->
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark">Isi Artikel</label>
                                                <textarea name="content" class="form-control" rows="6" placeholder="Masukkan konten artikel di sini..." required>{{ old('content', $a->content) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top px-4">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Delete Article -->
                        <div class="modal fade" id="deleteArticleModal{{ $a->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('admin.articles.destroy', $a->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-header border-bottom px-4">
                                            <h5 class="modal-title fw-bold text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Hapus Artikel</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4 text-center">
                                            <i class="fas fa-trash-alt fa-3x text-danger mb-3 opacity-75"></i>
                                            <h6 class="fw-bold text-dark">Hapus artikel '{{ $a->title }}'?</h6>
                                            <p class="text-muted small mb-0">Artikel akan dihapus dari sistem secara permanen.</p>
                                        </div>
                                        <div class="modal-footer border-top px-4">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">Ya, Hapus Artikel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Article (Sederhana: 1 Kotak Judul, 1 Kotak Isi, 1 Tombol Simpan) -->
<div class="modal fade" id="addArticleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('admin.articles.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom px-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-plus-circle me-2 text-success"></i> Tambah Artikel Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Kotak 1: Judul Artikel -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Judul Artikel</label>
                        <input type="text" name="title" class="form-control" placeholder="Masukkan judul artikel..." required>
                    </div>
                    <!-- Kotak 2: Isi Artikel -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Isi Artikel</label>
                        <textarea name="content" class="form-control" rows="6" placeholder="Masukkan konten artikel di sini..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">Simpan Artikel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
