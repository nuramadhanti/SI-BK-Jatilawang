@extends('layouts.dashboard')

@section('title', 'Jadwal Konseling')
@section('breadcumb', 'Jadwal Konseling')
@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="datatableJadwal">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Tipe</th>
                                <th>Jadwal Konseling</th>
                                <th>Deskripsi Permasalahan</th>
                                <th>Status</th>
                                <th>Skor Prioritas</th>
                                @if (auth()->user()->role === 'guru' && auth()->user()->guru && auth()->user()->guru->role_guru === 'bk')
                                    <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="jadwalKonselingTable">
                            @foreach ($jadwalKonseling as $jadwal)
                                <tr data-id="{{ $jadwal->id }}">
                                    <td>{{ $jadwal->siswa->user->name }}</td>
                                    <td>
                                        @if ($jadwal->report_type === 'self')
                                            <span class="text-primary">
                                                <i class="bi bi-person"></i> Laporan Siswa
                                            </span>
                                        @else
                                            <span class="text-dark">
                                                <i class="bi bi-person-badge"></i> Laporan Guru
                                            </span>
                                        @endif
                                    </td>

                                    <td>{{ \Carbon\Carbon::parse($jadwal->tanggal_disetujui)->format('d-m-Y H:i') }}</td>
                                    <td>{{ Str::limit($jadwal->deskripsi_permasalahan, 50) }}</td>
                                    <td>
                                        <span
                                            class="badge
                                            {{ $jadwal->status === 'menunggu'
                                                ? 'bg-warning'
                                                : ($jadwal->status === 'disetujui'
                                                    ? 'bg-success'
                                                    : ($jadwal->status === 'selesai'
                                                        ? 'bg-primary'
                                                        : 'bg-danger')) }}">
                                            {{ ucfirst($jadwal->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge 
                                                @if($jadwal->skor_prioritas >= 85)
                                                    bg-danger
                                                @elseif($jadwal->skor_prioritas >= 65)
                                                    bg-warning text-dark
                                                @elseif($jadwal->skor_prioritas >= 32.5)
                                                    bg-info
                                                @else
                                                    bg-secondary
                                                @endif
                                                fs-6">
                                                {{ $jadwal->skor_prioritas }}
                                            </span>
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" 
                                                title="{{ $jadwal->getRumusSkorAkhir() }}">
                                                <i class="bi bi-info-circle"></i>
                                            </button>
                                        </div>
                                        
                                        {{-- Breakdown Detail (Expandable) --}}
                                        <small class="text-muted d-block mt-2">
                                            <a class="text-decoration-none" style="cursor: pointer;" 
                                                onclick="toggleBreakdown(event, {{ $jadwal->id }})">
                                                <i class="bi bi-chevron-right"></i> Detail Perhitungan
                                            </a>
                                        </small>
                                        
                                        <div id="breakdown-{{ $jadwal->id }}" class="breakdown-detail mt-2" 
                                            style="display: none; font-size: 0.85rem;">
                                            @php $breakdown = $jadwal->getBreakdownSkor() @endphp
                                            <table class="table table-sm table-borderless">
                                                <tbody>
                                                    @foreach($breakdown as $item)
                                                        <tr>
                                                            <td class="ps-0">{{ $item['kriteria_nama'] }}:</td>
                                                            <td class="text-center" style="width: 120px;">
                                                                <code>{{ $item['skor_sub_kriteria'] }} × {{ $item['bobot'] }}</code>
                                                            </td>
                                                            <td class="text-end pe-0" style="width: 60px;">
                                                                <strong>{{ $item['skor_terbobot'] }}</strong>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    <tr class="border-top border-secondary">
                                                        <td colspan="2" class="ps-0 text-end"><strong>Skor Akhir:</strong></td>
                                                        <td class="text-end pe-0">
                                                            <strong class="text-primary">{{ $jadwal->skor_prioritas }}</strong>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>

                                    @if (auth()->user()->role === 'guru' && auth()->user()->guru && auth()->user()->guru->role_guru === 'bk')
                                        <td>
                                            @if ($jadwal->status === 'menunggu')
                                                <button class="btn btn-sm btn-success approve-jadwal"
                                                    data-id="{{ $jadwal->id }}" data-bs-toggle="modal"
                                                    data-bs-target="#approveModal"><i class="bi bi-check-circle"></i>
                                                    Setujui</button>
                                                <button class="btn btn-sm btn-danger reject-jadwal"
                                                    data-id="{{ $jadwal->id }}" data-bs-toggle="modal"
                                                    data-bs-target="#rejectModal"><i class="bi bi-x-circle"></i>
                                                    Tolak</button>
                                            @endif
                                            @if ($jadwal->status === 'disetujui')
                                                <button class="btn btn-sm btn-primary complete-jadwal"
                                                    data-id="{{ $jadwal->id }}" data-bs-toggle="modal"
                                                    data-bs-target="#completeModal"><i class="bi bi-check2-all"></i>
                                                    Selesai</button>
                                                <button class="btn btn-sm btn-warning edit-jadwal"
                                                    data-id="{{ $jadwal->id }}"
                                                    data-tanggal="{{ $jadwal->tanggal_disetujui }}"
                                                    data-tempat="{{ $jadwal->tempat }}" data-bs-toggle="modal"
                                                    data-bs-target="#editModal">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </button>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if (auth()->user()->role === 'guru' && auth()->user()->guru && auth()->user()->guru->role_guru === 'bk')
        <div class="modal fade" id="completeModal" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="completeModalLabel">Selesaikan Konseling</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="completeForm" method="POST" action="{{ route('permohonan-konseling.complete', 0) }}">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label for="rangkuman" class="form-label">Rangkuman Konseling</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-textarea"></i></span>
                                    <textarea class="form-control" id="rangkuman" name="rangkuman" placeholder="Masukkan rangkuman hasil konseling"
                                        required>{{ old('rangkuman') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Selesai</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Jadwal Konseling</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form id="editForm" method="POST" action="">
                        @csrf
                        @method('PATCH')

                        <div class="modal-body">

                            <div class="form-group mb-3">
                                <label class="form-label">Tanggal Konseling</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                    <input type="datetime-local" id="editTanggal" name="tanggal_disetujui"
                                        class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Tempat Konseling</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" id="editTempat" name="tempat" class="form-control"
                                        placeholder="Masukkan tempat" required>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.approve-jadwal').on('click', function() {
                $('#approveForm').attr('action', "{{ url('permohonan-konseling/approve') }}/" + $(this)
                    .data('id'));
            });

            $('.reject-jadwal').on('click', function() {
                $('#rejectForm').attr('action', "{{ url('permohonan-konseling/reject') }}/" + $(this).data(
                    'id'));
            });

            $('.complete-jadwal').on('click', function() {
                $('#completeForm').attr('action', "{{ url('permohonan-konseling/complete') }}/" + $(this)
                    .data('id'));
            });

            $('#datatableJadwal').DataTable({
                order: [
                    [5, 'desc']
                ],
                columnDefs: [{
                    orderable: false,
                    targets: -1
                }]
            });

            $('.edit-jadwal').on('click', function() {
                let id = $(this).data('id');
                let tanggal = $(this).data('tanggal');
                let tempat = $(this).data('tempat');

                let formattedDate = new Date(tanggal).toISOString().slice(0, 16);

                $('#editForm').attr('action', "{{ url('permohonan-konseling/edit-jadwal') }}/" + id);
                $('#editTanggal').val(formattedDate);
                $('#editTempat').val(tempat);
            });

            // Initialize tooltips untuk show rumus
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl, { html: true });
            });
        });

        // Toggle breakdown detail
        function toggleBreakdown(event, id) {
            event.preventDefault();
            const element = document.getElementById('breakdown-' + id);
            const icon = event.target.closest('i');
            
            if (element.style.display === 'none') {
                element.style.display = 'block';
                icon.classList.remove('bi-chevron-right');
                icon.classList.add('bi-chevron-down');
            } else {
                element.style.display = 'none';
                icon.classList.remove('bi-chevron-down');
                icon.classList.add('bi-chevron-right');
            }
        }
    </script>
@endsection
