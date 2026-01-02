@extends('layouts.dashboard')

@section('title', 'Laporan Konseling Siswa')
@section('breadcumb', 'Laporan Konseling Siswa')

@section('content')
    <div class="container-fluid mt-4">

        <div class="card shadow-sm border-0">
            <div class="card-header">
                <h4 class="mb-3">
                    <i class="bi bi-journal-text text-primary"></i> Laporan Konseling Siswa
                </h4>

                <form action="{{ route('laporan.index') }}" method="GET" class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label">Tahun Akademik</label>
                        <select name="tahun_akademik" class="form-select" required>
                            @foreach ($tahunAjaranList as $th)
                                <option value="{{ $th->id }}"
                                    {{ $request->tahun_akademik == $th->id ? 'selected' : '' }}>
                                    {{ $th->tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Kelas Siswa</label>
                        <select name="kelas" class="form-select">
                            <option value="">Semua Kelas</option>
                            @foreach ($kelasList as $k)
                                <option value="{{ $k->id }}" {{ $request->kelas == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                   <!-- <div class="col-md-3">
                        <label class="form-label">Kategori Masalah</label>
                        <select name="kategori_masalah" class="form-select">
                            <option value="">Semua Kategori</option>
                            <option value="Akademik" {{ request('kategori_masalah') == 'Akademik' ? 'selected' : '' }}>
                                Akademik
                            </option>
                            <option value="Karir" {{ request('kategori_masalah') == 'Karir' ? 'selected' : '' }}>
                                Karir
                            </option>
                            <option value="Pribadi" {{ request('kategori_masalah') == 'Pribadi' ? 'selected' : '' }}>
                                Pribadi
                            </option>
                            <option value="Sosial" {{ request('kategori_masalah') == 'Sosial' ? 'selected' : '' }}>
                                Sosial
                            </option>
                        </select>
                    </div> -->
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Pilih
                        </button>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <a href="{{ route('laporan.pdf', request()->all()) }}" class="btn btn-danger w-100" target="_blank">
                            <i class="bi bi-file-earmark-pdf"></i> Cetak
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-body">
                <!-- Tabs untuk Data dan Analytics -->
                <ul class="nav nav-tabs mb-3" id="laporanTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="data-tab" data-bs-toggle="tab" data-bs-target="#data-content" type="button">
                            <i class="bi bi-table"></i> Data Konseling
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics-content" type="button">
                            <i class="bi bi-bar-chart"></i> Analitik Kriteria
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="laporanTabContent">
                    <!-- Tab 1: Data -->
                    <div class="tab-pane fade show active" id="data-content" role="tabpanel">
                        <h5 class="fw-bold mb-3">Data Konseling Siswa</h5>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Masalah</th>
                                        <th>Skor Prioritas</th>
                                        <th>Detail Skor</th>
                                        <th>Penyelesaian</th>
                                        <th>Nama Konselor</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($laporan as $index => $row)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($row->tanggal_pengajuan)->translatedFormat('d F Y') }}</td>
                                            <td>{{ $row->siswa->user->name }}</td>
                                            <td>{{ $row->siswa->kelas->nama }}</td>
                                            <td>{{ Str::limit($row->deskripsi_permasalahan, 30) }}</td>
                                            <td>
                                                <span class="badge 
                                                    @if ($row->skor_prioritas >= 85) bg-danger
                                                    @elseif ($row->skor_prioritas >= 65) bg-warning text-dark
                                                    @elseif ($row->skor_prioritas >= 32.5) bg-info
                                                    @else bg-success
                                                    @endif">
                                                    {{ $row->skor_prioritas }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" 
                                                    data-bs-target="#detailSkorModal" 
                                                    onclick="showDetailSkor({{ $row->id }}, '{{ $row->siswa->user->name }}')">
                                                    <i class="bi bi-eye"></i> Lihat
                                                </button>
                                            </td>
                                            <td>{{ Str::limit($row->rangkuman ?? '-', 30) }}</td>
                                            <td>{{ $row->nama_konselor }}</td>
                                            <td>
                                                <span class="badge bg-success">{{ ucfirst($row->status) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach

                                    @if ($laporan->isEmpty())
                                        <tr>
                                            <td colspan="10" class="text-center text-muted">
                                                Tidak ada data konseling.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 2: Analytics -->
                    <div class="tab-pane fade" id="analytics-content" role="tabpanel">
                        <h5 class="fw-bold mb-4">Analitik Kriteria & Skor Prioritas</h5>

                        @if ($laporan->isEmpty())
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Tidak ada data konseling untuk ditampilkan.
                            </div>
                        @else
                            <!-- KPI Cards -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body">
                                            <div class="fs-6 opacity-75">Total Permohonan</div>
                                            <div class="fs-3 fw-bold">{{ $analytics['totalPermohonan'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Distribusi Skor Prioritas -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="bi bi-pie-chart"></i> Distribusi Skor Prioritas</h6>
                                        </div>
                                        <div class="card-body">
                                            @forelse ($analytics['skorDistribusi'] as $kategori => $jumlah)
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span class="fw-medium">{{ $kategori }}</span>
                                                        <span class="text-muted">{{ $jumlah }} permohonan</span>
                                                    </div>
                                                    <div class="progress" style="height: 20px;">
                                                        @php
                                                            $percentage = $analytics['totalPermohonan'] > 0 ? ($jumlah / $analytics['totalPermohonan']) * 100 : 0;
                                                            $color = match($kategori) {
                                                                'Sangat Tinggi (80-100)' => 'bg-danger',
                                                                'Tinggi (60-79)' => 'bg-warning',
                                                                'Sedang (40-59)' => 'bg-info',
                                                                'Rendah (<40)' => 'bg-success',
                                                                default => 'bg-secondary'
                                                            };
                                                        @endphp
                                                        <div class="progress-bar {{ $color }}" style="width: {{ $percentage }}%"></div>
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-muted">Tidak ada data</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <!-- Rata-rata Skor per Kriteria -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="bi bi-bar-chart-line"></i> Rata-rata Skor per Kriteria</h6>
                                        </div>
                                        <div class="card-body">
                                            @forelse ($analytics['skorPerKriteria'] as $nama => $skor)
                                                <div class="row mb-3">
                                                    <div class="col-7">
                                                        <small class="d-block text-muted">{{ ucfirst(str_replace('_', ' ', $nama)) }}</small>
                                                    </div>
                                                    <div class="col-5 text-end">
                                                        <span class="badge bg-info fs-6">{{ $skor }}</span>
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-muted">Tidak ada data</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Permohonan Prioritas Tertinggi -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="bi bi-star-fill text-warning"></i> Top 5 Permohonan Prioritas Tertinggi</h6>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-sm mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Siswa</th>
                                                        <th>Skor</th>
                                                        <th>Kategori Skor</th>
                                                        <th>Kriteria Terpilih</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($analytics['permohonanTertinggi'] as $item)
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $item->siswa->user->name }}</strong><br>
                                                                <small class="text-muted">{{ $item->siswa->kelas->nama }}</small>
                                                            </td>
                                                            <td>
                                                                <span class="badge fs-6
                                                                    @if ($item->skor_prioritas >= 80) bg-danger
                                                                    @elseif ($item->skor_prioritas >= 60) bg-warning
                                                                    @elseif ($item->skor_prioritas >= 40) bg-info
                                                                    @else bg-success
                                                                    @endif">
                                                                    {{ $item->skor_prioritas }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if ($item->skor_prioritas >= 80)
                                                                    <span class="badge bg-danger">Sangat Tinggi</span>
                                                                @elseif ($item->skor_prioritas >= 60)
                                                                    <span class="badge bg-warning">Tinggi</span>
                                                                @elseif ($item->skor_prioritas >= 40)
                                                                    <span class="badge bg-info">Sedang</span>
                                                                @else
                                                                    <span class="badge bg-success">Rendah</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="d-flex gap-1 flex-wrap">
                                                                    @foreach ($item->permohonanKriterias as $pk)
                                                                        <small class="badge bg-light text-dark" title="{{ $pk->subKriteria->label }}">
                                                                            {{ ucfirst(str_replace('_', ' ', $pk->kriteria->nama)) }}: {{ $pk->skor }}
                                                                        </small>
                                                                    @endforeach
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted">Tidak ada data</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Skor Breakdown -->
    <div class="modal fade" id="detailSkorModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-calculator"></i> Detail Perhitungan Skor Prioritas
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <h6>Siswa: <span id="detailSiswa" class="text-primary fw-bold">-</span></h6>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Rumus: 
                            <span class="text-monospace" style="font-size: 0.9rem;">
                                Skor Akhir = (k1 × bobot) + (k2 × bobot) + (k3 × bobot) + (k4 × bobot) + ...
                            </span>
                        </h6>
                    </div>

                    <div id="detailBreakdown" class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Kriteria</th>
                                    <th class="text-center" style="width: 180px;">Perhitungan</th>
                                    <th class="text-end" style="width: 100px;">Hasil</th>
                                </tr>
                            </thead>
                            <tbody id="breakdownBody">
                                <!-- Will be filled by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Store laporan data di JavaScript untuk quick access
        const laporanData = {
            @foreach ($laporan as $row)
                {{ $row->id }}: @json($row->only(['siswa', 'permohonanKriterias']) + ['skor_prioritas' => $row->skor_prioritas]),
            @endforeach
        };

        function showDetailSkor(id, namaSiswa) {
            document.getElementById('detailSiswa').textContent = namaSiswa;
            
            if (laporanData[id]) {
                const data = laporanData[id];
                let html = '';
                let totalSkor = 0;
                
                // Hanya ambil data permohonanKriterias untuk breakdown
                if (data.permohonanKriterias && data.permohonanKriterias.length > 0) {
                    data.permohonanKriterias.forEach(function(pk, index) {
                        const skor = pk.skor;
                        const bobot = pk.kriteria.bobot;
                        const hasil = (skor * bobot).toFixed(2);
                        totalSkor += parseFloat(hasil);
                        
                        html += `
                            <tr>
                                <td><strong>${pk.kriteria.nama}</strong></td>
                                <td class="text-center">
                                    <code>${skor} × ${bobot}</code>
                                </td>
                                <td class="text-end"><strong>${hasil}</strong></td>
                            </tr>
                        `;
                    });
                    
                    html += `
                        <tr class="table-light fw-bold border-top-2">
                            <td colspan="2" class="text-end">Skor Akhir:</td>
                            <td class="text-end text-primary" style="font-size: 1.1rem;">${totalSkor.toFixed(2)}</td>
                        </tr>
                    `;
                } else {
                    html = '<tr><td colspan="3" class="text-center text-muted">Tidak ada kriteria</td></tr>';
                }
                
                document.getElementById('breakdownBody').innerHTML = html;
            }
        }
    </script>
