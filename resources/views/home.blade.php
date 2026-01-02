@extends('layouts.dashboard')

@section('content')
    <div class="container">
        {{-- Dashboard Siswa --}}
        @if(auth()->user()->role === 'siswa')
            <div class="mb-4">
                <h4 class="font-weight-bold">Selamat datang, {{ auth()->user()->name }} 👋</h4>
                <p class="text-muted">Berikut Ringkasan Informasi Konseling Anda</p>
            </div>

            <div class="row">
                <!-- Statistik Cards -->
                <div class="col-6 col-lg-4 col-md-6 mb-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body px-4 py-4">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="stats-icon" style="background-color: rgba(54, 162, 235, 0.1); border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                        <i class="iconly-boldCalendar" style="color: #36a2eb; font-size: 24px;"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold mb-1">Jadwal Konseling</h6>
                                    <h5 class="font-extrabold mb-0">{{ $totalKonseling }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-4 col-md-6 mb-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body px-4 py-4">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="stats-icon" style="background-color: rgba(255, 193, 7, 0.1); border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                        <i class="iconly-boldSearch" style="color: #ffc107; font-size: 24px;"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold mb-1">Permohonan Pending</h6>
                                    <h5 class="font-extrabold mb-0">{{ $permohonanPending }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-4 col-md-6 mb-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body px-4 py-4">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="stats-icon" style="background-color: rgba(76, 175, 80, 0.1); border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                        <i class="iconly-boldTick-Square" style="color: #4caf50; font-size: 24px;"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold mb-1">Konseling Selesai</h6>
                                    <h5 class="font-extrabold mb-0">{{ $konselingSelesai }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jadwal Konseling Terbaru -->
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 font-weight-bold">📅 Jadwal Konseling Saya</h6>
                        </div>
                        <div class="card-body">
                            @if($jadwalKonseling->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr class="bg-light">
                                                <th class="text-muted font-semibold">Tanggal</th>
                                                <th class="text-muted font-semibold">Tempat</th>
                                                <th class="text-muted font-semibold">Konselor</th>
                                                <th class="text-muted font-semibold">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($jadwalKonseling as $jadwal)
                                                <tr>
                                                    <td>
                                                        <small class="font-weight-bold">
                                                            {{ \Carbon\Carbon::parse($jadwal->tanggal_disetujui)->format('d M Y, H:i') }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <small>{{ $jadwal->tempat ?? '-' }}</small>
                                                    </td>
                                                    <td>
                                                        <small>{{ $jadwal->nama_konselor ?? 'Belum ditentukan' }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success">{{ ucfirst($jadwal->status) }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="iconly-boldCalendar" style="font-size: 48px; color: #ccc;"></i>
                                    <p class="text-muted mt-3">Belum ada jadwal konseling</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Riwayat Permohonan -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 font-weight-bold">📋 Riwayat Permohonan Konseling</h6>
                        </div>
                        <div class="card-body">
                            @if($permohonanKonseling->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr class="bg-light">
                                                <th class="text-muted font-semibold">Tanggal Pengajuan</th>
                                                <th class="text-muted font-semibold">Deskripsi</th>
                                                <th class="text-muted font-semibold">Status</th>
                                                <th class="text-muted font-semibold">Alasan Penolakan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($permohonanKonseling as $permohonan)
                                                <tr>
                                                    <td>
                                                        <small>{{ \Carbon\Carbon::parse($permohonan->tanggal_pengajuan)->format('d M Y') }}</small>
                                                    </td>
                                                    <td>
                                                        <small>{{ Str::limit($permohonan->deskripsi_permasalahan, 30) }}</small>
                                                    </td>
                                                    <td>
                                                        @if($permohonan->status === 'disetujui')
                                                            <span class="badge bg-success">Disetujui</span>
                                                        @elseif($permohonan->status === 'ditolak')
                                                            <span class="badge bg-danger">Ditolak</span>
                                                        @elseif($permohonan->status === 'menunggu')
                                                            <span class="badge bg-warning">Menunggu</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ ucfirst($permohonan->status) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <small>{{ $permohonan->alasan_penolakan ?? '-' }}</small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="iconly-boldDocument" style="font-size: 48px; color: #ccc;"></i>
                                    <p class="text-muted mt-3">Belum ada permohonan konseling</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        {{-- Dashboard Orang Tua --}}
        @elseif(auth()->user()->role === 'orangtua')
            <div class="mb-4">
                <h4 class="font-weight-bold">Selamat datang, {{ auth()->user()->name }} 👋</h4>
                <p class="text-muted">Pantau perkembangan konseling anak Anda</p>
            </div>

            @if(isset($anak))
                <div class="row">
                    <!-- Statistik Cards -->
                    <div class="col-6 col-lg-4 col-md-6 mb-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-body px-4 py-4">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="stats-icon" style="background-color: rgba(54, 162, 235, 0.1); border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                            <i class="iconly-boldCalendar" style="color: #36a2eb; font-size: 24px;"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold mb-1">Jadwal Konseling</h6>
                                        <h5 class="font-extrabold mb-0">{{ $totalKonseling }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-4 col-md-6 mb-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-body px-4 py-4">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="stats-icon" style="background-color: rgba(255, 193, 7, 0.1); border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                            <i class="iconly-boldSearch" style="color: #ffc107; font-size: 24px;"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold mb-1">Permohonan Pending</h6>
                                        <h5 class="font-extrabold mb-0">{{ $permohonanPending }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-4 col-md-6 mb-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-body px-4 py-4">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="stats-icon" style="background-color: rgba(76, 175, 80, 0.1); border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                            <i class="iconly-boldTick-Square" style="color: #4caf50; font-size: 24px;"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold mb-1">Konseling Selesai</h6>
                                        <h5 class="font-extrabold mb-0">{{ $konselingSelesai }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-4 col-md-6 mb-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-body px-4 py-4">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="stats-icon" style="background-color: rgba(156, 39, 176, 0.1); border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                            <i class="iconly-boldUser" style="color: #9c27b0; font-size: 24px;"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold mb-1">Siswa (Anak)</h6>
                                        <h5 class="font-extrabold mb-0">{{ $anak->user->name }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Siswa -->
                <div class="row mt-4">
                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 font-weight-bold">👤 Data Siswa</h6>
                            </div>
                            <div class="card-body">
                                @if($anak->user->foto)
                                    <img src="{{ asset('storage/' . $anak->user->foto) }}" alt="{{ $anak->user->name }}" class="img-fluid rounded mb-3" style="width: 100%; height: auto; object-fit: cover;">
                                @endif
                                <p class="mb-2"><strong>Nama:</strong> <br><small>{{ $anak->user->name }}</small></p>
                                <p class="mb-2"><strong>NISN:</strong> <br><small>{{ $anak->nisn ?? '-' }}</small></p>
                                <p class="mb-2"><strong>Kelas:</strong> <br><small>{{ $anak->kelas->nama ?? '-' }}</small></p>
                                <p class="mb-0"><strong>Alamat:</strong> <br><small>{{ $anak->alamat ?? '-' }}</small></p>
                            </div>
                        </div>
                    </div>

                    <!-- Jadwal Konseling Terbaru -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 font-weight-bold">📅 Jadwal Konseling {{ $anak->user->name }}</h6>
                            </div>
                            <div class="card-body">
                                @if($jadwalKonseling->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                                <tr class="bg-light">
                                                    <th class="text-muted font-semibold">Tanggal</th>
                                                    <th class="text-muted font-semibold">Tempat</th>
                                                    <th class="text-muted font-semibold">Konselor</th>
                                                    <th class="text-muted font-semibold">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($jadwalKonseling as $jadwal)
                                                    <tr>
                                                        <td>
                                                            <small class="font-weight-bold">
                                                                {{ \Carbon\Carbon::parse($jadwal->tanggal_disetujui)->format('d M Y, H:i') }}
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <small>{{ $jadwal->tempat ?? '-' }}</small>
                                                        </td>
                                                        <td>
                                                            <small>{{ $jadwal->nama_konselor ?? 'Belum ditentukan' }}</small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-success">{{ ucfirst($jadwal->status) }}</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="iconly-boldCalendar" style="font-size: 48px; color: #ccc;"></i>
                                        <p class="text-muted mt-3">Belum ada jadwal konseling</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Permohonan -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 font-weight-bold">📋 Riwayat Permohonan Konseling {{ $anak->user->name }}</h6>
                            </div>
                            <div class="card-body">
                                @if($permohonanKonseling->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                                <tr class="bg-light">
                                                    <th class="text-muted font-semibold">Tanggal Pengajuan</th>
                                                    <th class="text-muted font-semibold">Deskripsi</th>
                                                    <th class="text-muted font-semibold">Status</th>
                                                    <th class="text-muted font-semibold">Alasan Penolakan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($permohonanKonseling as $permohonan)
                                                    <tr>
                                                        <td>
                                                            <small>{{ \Carbon\Carbon::parse($permohonan->tanggal_pengajuan)->format('d M Y') }}</small>
                                                        </td>
                                                        <td>
                                                            <small>{{ Str::limit($permohonan->deskripsi_permasalahan, 30) }}</small>
                                                        </td>
                                                        <td>
                                                            @if($permohonan->status === 'disetujui')
                                                                <span class="badge bg-success">Disetujui</span>
                                                            @elseif($permohonan->status === 'ditolak')
                                                                <span class="badge bg-danger">Ditolak</span>
                                                            @elseif($permohonan->status === 'menunggu')
                                                                <span class="badge bg-warning">Menunggu</span>
                                                            @else
                                                                <span class="badge bg-secondary">{{ ucfirst($permohonan->status) }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <small>{{ $permohonan->alasan_penolakan ?? '-' }}</small>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="iconly-boldDocument" style="font-size: 48px; color: #ccc;"></i>
                                        <p class="text-muted mt-3">Belum ada permohonan konseling</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Guru BK -->
                <div class="row mt-4 mb-4">
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="iconly-boldWarning"></i> Data siswa/anak Anda belum ditemukan di sistem. Silakan hubungi admin.
                </div>
            @endif
        {{-- Dashboard Guru/Admin --}}
        @else
            <div class="row">
                <!-- Stats Cards -->
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card shadow-lg">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon green mb-2">
                                        <i class="iconly-boldAdd-User"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Siswa</h6>
                                    <h6 class="font-extrabold mb-0">{{ $countSiswa }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card shadow-lg">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon yellow mb-2">
                                        <i class="iconly-boldUser"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Guru</h6>
                                    <h6 class="font-extrabold mb-0">{{ $countGuru }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card  shadow-lg">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon blue mb-2">
                                        <i class="iconly-boldCredit-Card"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Walikelas</h6>
                                    <h6 class="font-extrabold mb-0">{{ $countWalikelas }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card  shadow-lg">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon blue mb-2">
                                        <i class="iconly-boldCredit-Card"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Guru BK</h6>
                                    <h6 class="font-extrabold mb-0">{{ $countGuruBk }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
