@extends('layouts.dashboard')

@section('title', 'Sub-Kriteria')
@section('breadcumb', 'Sub-Kriteria')
@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <a href="{{ route('kriteria.index') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <span class="text-muted">Kriteria: <strong>{{ ucfirst(str_replace('_', ' ', $kriteria->nama)) }}</strong></span>
                    </div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#subKriteriaModal">
                        <i class="bi bi-plus-circle"></i> Tambah Sub-Kriteria
                    </button>
                </div>
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
                                <th>Label</th>
                                <th>Skor</th>
                                <th>Urutan</th>
                                <th>Status</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="subKriteriaTable">
                            @foreach ($subKriterias as $subKriteria)
                                <tr data-id="{{ $subKriteria->id }}">
                                    <td>{{ $subKriteria->label }}</td>
                                    <td><span class="badge bg-primary">{{ $subKriteria->skor }}</span></td>
                                    <td>{{ $subKriteria->urutan }}</td>
                                    <td>
                                        @if ($subKriteria->aktif)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($subKriteria->deskripsi, 50) }}</td>
                                    <td class="d-flex text-nowrap gap-2">
                                        <button class="btn btn-sm btn-warning edit-sub-kriteria" 
                                            data-id="{{ $subKriteria->id }}"
                                            data-label="{{ $subKriteria->label }}" 
                                            data-skor="{{ $subKriteria->skor }}"
                                            data-urutan="{{ $subKriteria->urutan }}" 
                                            data-deskripsi="{{ $subKriteria->deskripsi }}"
                                            data-aktif="{{ $subKriteria->aktif }}" 
                                            data-bs-toggle="modal"
                                            data-bs-target="#subKriteriaModal"><i class="bi bi-pencil"></i> Edit</button>
                                        <form action="{{ route('kriteria.sub-kriteria.destroy', [$kriteria, $subKriteria]) }}" method="POST"
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

    <!-- Modal Sub-Kriteria -->
    <div class="modal fade" id="subKriteriaModal" tabindex="-1" aria-labelledby="subKriteriaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subKriteriaModalLabel">Tambah Sub-Kriteria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="subKriteriaForm" method="POST" action="{{ route('kriteria.sub-kriteria.store', $kriteria) }}">
                    @csrf
                    <input type="hidden" name="_method" id="form_method" value="POST">
                    <input type="hidden" name="id" id="sub_kriteria_id">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="label" class="form-label">Label</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                <input type="text" class="form-control" id="label" name="label"
                                    placeholder="Contoh: Sangat Mendesak" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="skor" class="form-label">Skor (0-100)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-percent"></i></span>
                                <input type="number" class="form-control" id="skor" name="skor" min="0" max="100"
                                    placeholder="90" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-textarea"></i></span>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"
                                    placeholder="Jelaskan sub-kriteria ini"></textarea>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="urutan" class="form-label">Urutan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-sort-numeric-down"></i></span>
                                <input type="number" class="form-control" id="urutan" name="urutan" min="1"
                                    placeholder="1" required>
                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="aktif" name="aktif" value="1" checked>
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
        // Edit Sub-Kriteria
        document.querySelectorAll('.edit-sub-kriteria').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                document.getElementById('sub_kriteria_id').value = id;
                document.getElementById('label').value = this.dataset.label;
                document.getElementById('skor').value = this.dataset.skor;
                document.getElementById('urutan').value = this.dataset.urutan;
                document.getElementById('deskripsi').value = this.dataset.deskripsi;
                document.getElementById('aktif').checked = this.dataset.aktif == 1;
                document.getElementById('subKriteriaModalLabel').textContent = 'Edit Sub-Kriteria';
                document.getElementById('subKriteriaForm').action = `/kriteria/{{ $kriteria->id }}/sub-kriteria/${id}`;
                document.getElementById('form_method').value = 'PUT';
            });
        });

        // Reset Modal untuk Tambah
        document.querySelector('.btn-primary[data-bs-target="#subKriteriaModal"]').addEventListener('click', function() {
            document.getElementById('sub_kriteria_id').value = '';
            document.getElementById('subKriteriaForm').reset();
            document.getElementById('subKriteriaModalLabel').textContent = 'Tambah Sub-Kriteria';
            document.getElementById('subKriteriaForm').action = '{{ route('kriteria.sub-kriteria.store', $kriteria) }}';
            document.getElementById('form_method').value = 'POST';
            document.getElementById('aktif').checked = true;
        });
    </script>
@endsection
