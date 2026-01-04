@extends('layouts.dashboard')

@section('title', 'Kriteria')
@section('breadcumb', 'Kriteria')
@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#kriteriaModal">
                    <i class="bi bi-plus-circle"></i> Tambah
                </button>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="datatable">
                        <thead>
                            <tr>
                                <th>Nama Kriteria</th>
                                <th>Bobot</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="kriteriaTable">
                            @foreach ($kriterias as $kriteria)
                                <tr data-id="{{ $kriteria->id }}">
                                    <td>{{ ucfirst(str_replace('_', ' ', $kriteria->nama)) }}</td>
                                    <td><span class="badge bg-info">{{ $kriteria->bobot * 100 }}%</span></td>
                                    <td>
                                        @if ($kriteria->aktif)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="d-flex text-nowrap gap-2">
                                        <a href="{{ route('kriteria.sub-kriteria.index', $kriteria) }}" 
                                            class="btn btn-sm btn-info"><i class="bi bi-list-check"></i> Sub-Kriteria</a>
                                        <button class="btn btn-sm btn-warning edit-kriteria" data-id="{{ $kriteria->id }}"
                                            data-nama="{{ $kriteria->nama }}" data-deskripsi="{{ $kriteria->deskripsi }}"
                                            data-bobot="{{ $kriteria->bobot }}"
                                            data-aktif="{{ $kriteria->aktif }}" data-bs-toggle="modal"
                                            data-bs-target="#kriteriaModal"><i class="bi bi-pencil"></i> Edit</button>
                                        <form action="{{ route('kriteria.destroy', $kriteria->id) }}" method="POST"
                                            style="display:inline;"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i>
                                                Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Kriteria -->
    <div class="modal fade" id="kriteriaModal" tabindex="-1" aria-labelledby="kriteriaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="kriteriaModalLabel">Tambah Kriteria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="kriteriaForm" method="POST" action="{{ route('kriteria.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="form_method" value="POST">
                    <input type="hidden" name="id" id="kriteria_id">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="nama" class="form-label">Nama Kriteria</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                <input type="text" class="form-control" id="nama" name="nama"
                                    placeholder="Contoh: tingkat_urgensi" required>
                            </div>
                            <small class="form-text text-muted">Gunakan huruf kecil dan underscore (_) untuk spasi</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-textarea"></i></span>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"
                                    placeholder="Jelaskan kriteria ini"></textarea>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="bobot" class="form-label">Bobot (0-1)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-percent"></i></span>
                                <input type="number" class="form-control" id="bobot" name="bobot" step="0.01"
                                    min="0" max="1" placeholder="0.4" required>
                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="aktif" name="aktif" value="1">
                            <label class="form-check-label" for="aktif">
                                Aktif
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        // Edit Kriteria
        document.querySelectorAll('.edit-kriteria').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                document.getElementById('kriteria_id').value = id;
                document.getElementById('nama').value = this.dataset.nama;
                document.getElementById('deskripsi').value = this.dataset.deskripsi;
                document.getElementById('bobot').value = this.dataset.bobot;
                document.getElementById('aktif').checked = this.dataset.aktif == 1;
                document.getElementById('kriteriaModalLabel').textContent = 'Edit Kriteria';
                document.getElementById('kriteriaForm').action = `/kriteria/${id}`;
                document.getElementById('form_method').value = 'PUT';
            });
        });

        // Reset Modal untuk Tambah
        document.querySelector('.btn-primary[data-bs-target="#kriteriaModal"]').addEventListener('click', function() {
            document.getElementById('kriteria_id').value = '';
            document.getElementById('kriteriaForm').reset();
            document.getElementById('kriteriaModalLabel').textContent = 'Tambah Kriteria';
            document.getElementById('kriteriaForm').action = '{{ route('kriteria.store') }}';
            document.getElementById('form_method').value = 'POST';
        });
    </script>
@endsection
