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

                <h5 class="fw-bold mb-3">Data Konseling Siswa</h5>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Kategori Konseling</th>
                                <th>Masalah</th>
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
                                    <td>{{ $row->kategori_masalah_label }}</td>
                                    <td>{{ Str::limit($row->deskripsi_permasalahan, 50) }}</td>
                                    <td>{{ Str::limit($row->rangkuman ?? '-', 50) }}</td>
                                    <td>{{ $row->nama_konselor }}</td>
                                    <td>
                                        <span class="badge bg-success">{{ ucfirst($row->status) }}</span>
                                    </td>
                                </tr>
                            @endforeach

                            @if ($laporan->isEmpty())
                                <tr>
                                    <td colspan="9" class="text-center text-muted">
                                        Tidak ada data konseling.
                                    </td>

                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
