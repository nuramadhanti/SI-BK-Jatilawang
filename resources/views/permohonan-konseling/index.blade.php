@extends('layouts.dashboard')

@section('title', 'Permohonan Konseling')
@section('breadcumb', 'Permohonan Konseling')
@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                @if (auth()->user()->role === 'siswa' ||
                        (auth()->user()->role === 'guru' && auth()->user()->guru && auth()->user()->guru->role_guru === 'walikelas'))
                    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal"
                        data-bs-target="#permohonanKonselingModal">
                        <i class="bi bi-plus-circle"></i> Buat Permohonan
                    </button>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="datatablePermohonan">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Tipe</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Deskripsi Permasalahan</th>
                                <th>Status</th>
                                <th>Skor Prioritas</th>
                                <th>Bukti</th>
                                @if (auth()->user()->role === 'guru' && auth()->user()->guru && auth()->user()->guru->role_guru === 'bk')
                                    <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="permohonanKonselingTable">
                            @foreach ($permohonanKonseling as $permohonan)
                                <tr data-id="{{ $permohonan->id }}">
                                    <td>{{ $permohonan->siswa->user->name }}</td>
                                    <td>
                                        @if ($permohonan->report_type === 'self')
                                            <span class="text-primary">
                                                <i class="bi bi-person"></i> Laporan Siswa
                                            </span>
                                        @else
                                            <span class="text-dark">
                                                <i class="bi bi-person-badge"></i> Laporan Guru
                                            </span>
                                        @endif
                                    </td>

                                    <td>{{ \Carbon\Carbon::parse($permohonan->tanggal_pengajuan)->format('d-m-Y') }}</td>

                                    <td>{{ Str::limit($permohonan->deskripsi_permasalahan, 50) }}</td>

                                    {{-- STATUS --}}
                                    <td>
                                        <span
                                            class="badge
                                                {{ $permohonan->status === 'menunggu'
                                                    ? 'bg-warning'
                                                    : ($permohonan->status === 'disetujui'
                                                        ? 'bg-success'
                                                        : ($permohonan->status === 'selesai'
                                                            ? 'bg-primary'
                                                            : 'bg-danger')) }}">
                                            {{ ucfirst($permohonan->status) }}
                                        </span>

                                        @if ($permohonan->status === 'ditolak' && $permohonan->alasan_penolakan)
                                            <div class="mt-1 small text-danger">
                                                <i class="bi bi-info-circle"></i> {{ $permohonan->alasan_penolakan }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- SKOR PRIORITAS DENGAN BREAKDOWN --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge 
                                                @if($permohonan->skor_prioritas >= 85)
                                                    bg-danger
                                                @elseif($permohonan->skor_prioritas >= 65)
                                                    bg-warning text-dark
                                                @elseif($permohonan->skor_prioritas >= 32.5)
                                                    bg-info
                                                @else
                                                    bg-secondary
                                                @endif
                                                fs-6">
                                                {{ $permohonan->skor_prioritas }}
                                            </span>
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" 
                                                title="{{ $permohonan->getRumusSkorAkhir() }}">
                                                <i class="bi bi-info-circle"></i>
                                            </button>
                                        </div>
                                        
                                        {{-- Breakdown Detail (Expandable) --}}
                                        <small class="text-muted d-block mt-2">
                                            <a class="text-decoration-none" style="cursor: pointer;" 
                                                onclick="toggleBreakdown(event, {{ $permohonan->id }})">
                                                <i class="bi bi-chevron-right"></i> Detail Perhitungan
                                            </a>
                                        </small>
                                        
                                        <div id="breakdown-{{ $permohonan->id }}" class="breakdown-detail mt-2" 
                                            style="display: none; font-size: 0.85rem;">
                                            @php $breakdown = $permohonan->getBreakdownSkor() @endphp
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
                                                            <strong class="text-primary">{{ $permohonan->skor_prioritas }}</strong>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>

                                    <td>
                                        @if ($permohonan->bukti_masalah)
                                            @php
                                                $ext = strtolower(
                                                    pathinfo($permohonan->bukti_masalah, PATHINFO_EXTENSION),
                                                );
                                            @endphp

                                            {{-- FOTO --}}
                                            @if (in_array($ext, ['jpg', 'jpeg', 'png']))
                                                <a href="{{ asset('storage/' . $permohonan->bukti_masalah) }}"
                                                    target="_blank">
                                                    <img src="{{ asset('storage/' . $permohonan->bukti_masalah) }}"
                                                        width="60" class="img-thumbnail">
                                                </a>

                                                {{-- VIDEO --}}
                                            @elseif (in_array($ext, ['mp4', 'mov', 'avi']))
                                                <video width="100" controls>
                                                    <source src="{{ asset('storage/' . $permohonan->bukti_masalah) }}">
                                                </video>

                                                {{-- FILE LAIN --}}
                                            @else
                                                <a href="{{ asset('storage/' . $permohonan->bukti_masalah) }}"
                                                    class="btn btn-info btn-sm" target="_blank">
                                                    <i class="bi bi-eye"></i> Lihat File
                                                </a>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    @if (auth()->user()->role === 'guru' && auth()->user()->guru && auth()->user()->guru->role_guru === 'bk')
                                        <td>
                                            @if ($permohonan->status === 'menunggu')
                                                <button class="btn btn-sm btn-success approve-permohonan"
                                                    data-id="{{ $permohonan->id }}" data-bs-toggle="modal"
                                                    data-bs-target="#approveModal">
                                                    <i class="bi bi-check-circle"></i> Setujui
                                                </button>

                                                <button class="btn btn-sm btn-danger reject-permohonan"
                                                    data-id="{{ $permohonan->id }}" data-bs-toggle="modal"
                                                    data-bs-target="#rejectModal">
                                                    <i class="bi bi-x-circle"></i> Tolak
                                                </button>
                                            @endif

                                            @if ($permohonan->status === 'disetujui')
                                                <button class="btn btn-sm btn-primary complete-permohonan"
                                                    data-id="{{ $permohonan->id }}" data-bs-toggle="modal"
                                                    data-bs-target="#completeModal">
                                                    <i class="bi bi-check2-all"></i> Selesai
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

    @if (auth()->user()->role === 'siswa' ||
            (auth()->user()->role === 'guru' && auth()->user()->guru && auth()->user()->guru->role_guru === 'walikelas'))
        <div class="modal fade" id="permohonanKonselingModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-pencil-square"></i> Buat Permohonan Konseling
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <form method="POST" action="{{ route('permohonan-konseling.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">

                            @if (auth()->user()->role === 'guru' && auth()->user()->guru->role_guru === 'walikelas')
                                <div class="mb-3">
                                    <label class="form-label">Siswa</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <select class="form-control" name="siswa_id" required>
                                            <option value="">Pilih Siswa</option>
                                            @foreach ($siswaWali as $siswa)
                                                <option value="{{ $siswa->id }}">{{ $siswa->user->name }} -
                                                    {{ $siswa->nisn }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            {{-- KRITERIA DINAMIS --}}
                            <div id="kriteriaContainer" class="mb-3">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span>Loading kriteria...</span>
                            </div>

                            {{-- Deskripsi --}}
                            <div class="mb-4">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <label class="form-label fw-bold mb-0" style="font-size: 1.1rem;">
                                        <i class="bi bi-file-text"></i> Deskripsi Permasalahan
                                    </label>
                                    <span class="text-danger">*</span>
                                </div>
                                
                                <div class="alert alert-info small mb-3" style="background-color: #e7f3ff; border-left: 4px solid #0d6efd; color: #004085;">
                                    <strong style="color: #004085;"><i class="bi bi-lightbulb-fill"></i> Tips Penulisan:</strong>
                                    <ul class="mb-0 mt-2 ps-3" style="color: #004085;">
                                        <li><strong>Kapan?</strong> Sejak kapan masalah ini terjadi?</li>
                                        <li><strong>Apa?</strong> Jelaskan kejadian/situasi secara detail</li>
                                        <li><strong>Siapa?</strong> Siapa saja yang terlibat?</li>
                                        <li><strong>Dampak?</strong> Bagaimana pengaruhnya pada Anda?</li>
                                        <li><strong>Solusi?</strong> Apa yang sudah dicoba?</li>
                                    </ul>
                                </div>
                                
                                <textarea class="form-control border-2" 
                                          id="deskripsi_permasalahan"
                                          name="deskripsi_permasalahan" 
                                          rows="5" 
                                          maxlength="2000"
                                          required
                                          placeholder="Contoh: Saya mengalami kesulitan belajar matematika sejak semester lalu. Saya sudah mencoba belajar sendiri tapi masih tidak paham. Guru saya kurang sabar menjelaskan. Nilai saya jadi jelek dan saya khawatir tidak naik kelas..."
                                          style="font-size: 0.95rem; line-height: 1.5;"></textarea>
                                
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle"></i> 
                                        Maksimal 2000 karakter.
                                    </small>
                                    <small class="text-muted">
                                        <span id="charCount" class="fw-semibold">0</span>/2000
                                    </small>
                                </div>
                            </div>

                            {{-- SKOR Akhir --}}

                            <div class="mb-4">
                                <div class="alert alert-primary mb-0" style="border-left: 5px solid #0d6efd;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><i class="bi bi-calculator"></i> Skor Prioritas Anda</strong>
                                            <small class="d-block text-white-50">Berdasarkan pilihan kriteria di atas</small>
                                        </div>
                                        <span id="skorEstimasi" class="badge bg-primary rounded-pill" style="font-size: 1.2rem; padding: 0.5rem 1rem;">0</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Upload Bukti Masalah --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-paperclip"></i> Upload Bukti Masalah
                                    <span class="text-muted" style="font-weight: normal;">(Opsional)</span>
                                </label>
                                <input type="file" class="form-control form-control-lg" name="bukti_masalah"
                                    accept="image/*,video/*"
                                    style="cursor: pointer;">
                                <small class="text-muted d-block mt-2">
                                    <i class="bi bi-info-circle"></i>
                                    Format yang didukung: JPG, PNG, MP4, MOV
                                </small>
                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle"></i> Batal
                            </button>
                            <button class="btn btn-primary btn-lg">
                                <i class="bi bi-send-fill"></i> Kirim Permohonan
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    @endif
    @if (auth()->user()->role === 'guru' && auth()->user()->guru && auth()->user()->guru->role_guru === 'bk')
        <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="approveModalLabel">Setujui Permohonan Konseling</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="approveForm" method="POST" action="{{ route('permohonan-konseling.approve', 0) }}">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label for="tanggal_disetujui" class="form-label">Tanggal Konseling</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                    <input type="datetime-local" class="form-control" id="tanggal_disetujui"
                                        name="tanggal_disetujui"
                                        value="{{ old('tanggal_disetujui', now()->format('Y-m-d\TH:i')) }}" required>
                                </div>

                            </div>
                            <div class="form-group mb-3">
                                <label for="tempat" class="form-label">Tempat Konseling</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" class="form-control" id="tempat" name="tempat"
                                        placeholder="Masukkan tempat konseling" value="{{ old('tempat') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Setujui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Tolak Permohonan (BK) -->
        <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="rejectModalLabel">
                            <i class="bi bi-x-circle-fill"></i> Tolak Permohonan Konseling
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="rejectForm" method="POST" action="{{ route('permohonan-konseling.reject', 0) }}">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body">
                            <div class="alert alert-warning" role="alert">
                                <i class="bi bi-exclamation-triangle"></i>
                                <strong>Perhatian:</strong> Pastikan Anda memberikan alasan yang jelas dan konstruktif agar siswa memahami mengapa permohonan ditolak.
                            </div>

                            <div class="form-group mb-3">
                                <label for="alasan_penolakan" class="form-label">
                                    <strong>Alasan Penolakan</strong>
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="alasan_penolakan" name="alasan_penolakan"
                                    placeholder="Tuliskan alasan penolakan dengan jelas dan ringkas..."
                                    minlength="10" maxlength="500" rows="4" required></textarea>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Minimal 10 karakter</small>
                                    <small class="text-muted"><span id="charCount">0</span>/500</small>
                                </div>
                            </div>

                            <div class="card bg-light">
                                <div class="card-body">
                                    <strong class="card-title">Tips Penulisan Alasan:</strong>
                                    <ul class="small mb-0 mt-2">
                                        <li>Jelaskan dengan bahasa yang sopan dan profesional</li>
                                        <li>Berikan alternative atau saran untuk follow-up</li>
                                        <li>Hindari kalimat yang negatif atau menyerang</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-x-circle"></i> Tolak Permohonan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- Modal Selesai Konseling (BK) -->
        <div class="modal fade" id="completeModal" tabindex="-1" aria-labelledby="completeModalLabel"
            aria-hidden="true">
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
    @endif
@endsection

@section('styles')
    <style>
        /* Ensure guidance text visibility in checked buttons */
        .btn-check:checked + .btn-outline-primary,
        .btn-check:checked + label {
            color: #000 !important;
            background-color: #e7f3ff !important;
            border-color: #0d6efd !important;
        }
        
        .btn-check:checked + .btn-outline-primary .small,
        .btn-check:checked + label .small {
            color: #666 !important;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let jumlahRiwayatSelesai = 0;

            // Load kriteria saat modal dibuka
            $('#permohonanKonselingModal').on('show.bs.modal', function() {
                if (!$(this).data('loaded')) {
                    loadRiwayatSelesai();
                    loadKriteria();
                    $(this).data('loaded', true);
                }
            });

            // Load jumlah riwayat yang sudah selesai
            function loadRiwayatSelesai() {
                $.ajax({
                    url: "{{ route('api.kriteria.index') }}?include_riwayat_count=1",
                    type: 'GET',
                    success: function(response) {
                        if (response.jumlah_riwayat_selesai !== undefined) {
                            jumlahRiwayatSelesai = response.jumlah_riwayat_selesai;
                        }
                    },
                    error: function() {
                        // Fallback jika API error
                        jumlahRiwayatSelesai = 0;
                    }
                });
            }

            // Load kriteria from API
            function loadKriteria() {
                $.ajax({
                    url: "{{ route('api.kriteria.index') }}",
                    type: 'GET',
                    success: function(response) {
                        let html = '';
                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function(kriteria, index) {
                                // Tampilkan informasi untuk kriteria riwayat konseling (diisi otomatis)
                                if (kriteria.nama === 'riwayat_konseling') {
                                    html += `
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">
                                                ${kriteria.nama}
                                                <span class="badge bg-secondary">${kriteria.bobot * 100}%</span>
                                            </label>
                                            <small class="text-muted d-block mb-2">${kriteria.deskripsi || ''}</small>
                                            <div class="alert alert-info" role="alert">
                                                <i class="bi bi-info-circle"></i> <strong>Riwayat Konseling Bulan ini : </strong><strong>${jumlahRiwayatSelesai}</strong> Akan digunakan untuk menentukan skor otomatis.
                                            </div>
                                        </div>
                                    `;
                                    return;
                                }

                                html += `
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <label class="form-label fw-bold mb-0" style="font-size: 1.1rem;">
                                                <i class="bi bi-list-check"></i> ${kriteria.nama.replace(/_/g, ' ').toUpperCase()}
                                            </label>
                                            <span class="badge bg-secondary">${kriteria.bobot * 100}%</span>
                                        </div>
                                        <small class="text-muted d-block mb-3">${kriteria.deskripsi || ''}</small>
                                        <div class="row g-2">
                                `;
                                
                                if (kriteria.sub_kriterias && kriteria.sub_kriterias.length > 0) {
                                    kriteria.sub_kriterias.forEach(function(subKriteria) {
                                        // Determine icon & color based on skor
                                        let icon = '🔵';
                                        let borderColor = '#0d6efd';
                                        if (subKriteria.skor >= 70) {
                                            icon = '🔴';
                                            borderColor = '#dc3545';
                                        } else if (subKriteria.skor >= 40) {
                                            icon = '🟡';
                                            borderColor = '#ffc107';
                                        }
                                        
                                        html += `
                                            <div class="col-12">
                                                <input type="radio" class="btn-check sub-kriteria-input" 
                                                    name="sub_kriteria_${kriteria.id}" 
                                                    id="sub_kriteria_${subKriteria.id}"
                                                    value="${subKriteria.id}"
                                                    data-kriteria-id="${kriteria.id}"
                                                    data-skor="${subKriteria.skor}"
                                                    data-label="${subKriteria.label}"
                                                    required>
                                                <label class="btn btn-outline-primary text-start w-100 p-3" for="sub_kriteria_${subKriteria.id}" style="border-left: 5px solid ${borderColor}; transition: all 0.2s; color: #000 !important;">
                                                    <div class="d-flex justify-content-between align-items-start w-100 gap-3">
                                                        <div class="flex-grow-1">
                                                            <div class="fw-semibold text-dark d-flex align-items-center gap-2">
                                                                <span>${icon}</span>
                                                                <span>${subKriteria.label}</span>
                                                            </div>
                                                            ${subKriteria.guidance_text ? `<div class="small mt-2 ps-4 lh-sm" style="color: #666 !important;">${subKriteria.guidance_text}</div>` : ''}
                                                        </div>
                                                        <span class="badge bg-primary rounded-pill flex-shrink-0 mt-1">${subKriteria.skor}</span>
                                                    </div>
                                                </label>
                                            </div>
                                        `;
                                    });
                                }
                                
                                html += `
                                        </div>
                                    </div>
                                `;
                            });
                            
                            $('#kriteriaContainer').html(html);
                            
                            // Attach change handlers untuk calculate skor
                            $(document).on('change', '.sub-kriteria-input', function() {
                                calculateSkor();
                            });
                        } else {
                            $('#kriteriaContainer').html('<div class="alert alert-warning">Tidak ada kriteria tersedia</div>');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading kriteria:', xhr);
                        $('#kriteriaContainer').html('<div class="alert alert-danger">Gagal memuat kriteria</div>');
                    }
                });
            }

            // Character counter untuk deskripsi_permasalahan
            $('#deskripsi_permasalahan').on('input', function() {
                const charCount = $(this).val().length;
                $('#charCount').text(charCount);
                
                // Add visual feedback only for maxlength
                if (charCount >= 2000) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                } else {
                    $(this).removeClass('is-valid is-invalid');
                }
            });

            // Calculate skor estimasi
            function calculateSkor() {
                let totalSkor = 0;
                let selectedKriterias = {};
                
                $('.sub-kriteria-input:checked').each(function() {
                    let kriteriaId = $(this).data('kriteria-id');
                    let skor = parseInt($(this).data('skor'));
                    selectedKriterias[kriteriaId] = skor;
                    totalSkor += skor;
                });
                
                $('#skorEstimasi').text(totalSkor);
                $('#skorEstimasi').removeClass('bg-primary bg-warning bg-danger bg-success');
                
                if (totalSkor >= 250) {
                    $('#skorEstimasi').addClass('bg-danger');
                } else if (totalSkor >= 200) {
                    $('#skorEstimasi').addClass('bg-warning');
                } else if (totalSkor >= 100) {
                    $('#skorEstimasi').addClass('bg-info');
                } else {
                    $('#skorEstimasi').addClass('bg-primary');
                }
            }

            $('.approve-permohonan').on('click', function() {
                $('#approveForm').attr('action', "{{ url('permohonan-konseling/approve') }}/" + $(this)
                    .data('id'));
            });

            $('.reject-permohonan').on('click', function() {
                $('#rejectForm').attr('action', "{{ url('permohonan-konseling/reject') }}/" + $(this).data(
                    'id'));
                // Reset form and char counter
                $('#alasan_penolakan').val('');
                $('#charCount').text('0');
            });

            // Character counter untuk alasan_penolakan
            $('#alasan_penolakan').on('input', function() {
                const charCount = $(this).val().length;
                $('#charCount').text(charCount);
                
                // Change color based on length
                if (charCount < 10) {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                }
            });

            $('.complete-permohonan').on('click', function() {
                $('#completeForm').attr('action', "{{ url('permohonan-konseling/complete') }}/" + $(this)
                    .data('id'));
            });

            $('#datatablePermohonan').DataTable({
                order: [
                    [5, 'desc']
                ],
                columnDefs: [{
                    orderable: false,
                    targets: -1
                }]
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
