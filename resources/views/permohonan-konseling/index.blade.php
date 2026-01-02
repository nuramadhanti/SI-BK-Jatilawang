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

                                    {{-- SKOR PRIORITAS --}}
                                    <td>
                                        <strong>{{ $permohonan->skor_prioritas }}</strong>
                                        <div class="text-muted small">
                                            <div>Urgensi: {{ $permohonan->tingkat_urgensi_label }}
                                                ({{ $permohonan->tingkat_urgensi_skor }})</div>
                                            <div>Dampak: {{ $permohonan->dampak_masalah_label }}
                                                ({{ $permohonan->dampak_masalah_skor }})</div>
                                            <div>Kategori: {{ $permohonan->kategori_masalah_label }}
                                                ({{ $permohonan->kategori_masalah_skor }})</div>
                                            <div>Riwayat: {{ $permohonan->riwayat_konseling_label }}
                                                ({{ $permohonan->riwayat_konseling_skor }})</div>
                                        </div>
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

                    <div class="modal-header">
                        <h5 class="modal-title">Buat Permohonan Konseling</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form method="POST" action="{{ route('permohonan-konseling.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="modal-body">

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

                            {{-- Tingkat Urgensi --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    Tingkat Urgensi
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lightning-charge"></i></span>
                                    <select class="form-control" name="tingkat_urgensi_skor" required
                                        onchange="document.getElementById('tingkat_urgensi_label').value=this.options[this.selectedIndex].text;">
                                        <option value="">Pilih Tingkat Urgensi</option>
                                        <option value="20">Tidak Mendesak</option>
                                        <option value="40">Cukup Mendesak </option>
                                        <option value="70">Mendesak </option>
                                        <option value="90">Sangat Mendesak</option>
                                    </select>
                                    <input type="hidden" name="tingkat_urgensi_label" id="tingkat_urgensi_label">
                                </div>
                            </div>

                            {{-- Dampak Masalah --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    Dampak Masalah
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-bar-chart"></i></span>
                                    <select class="form-control" name="dampak_masalah_skor" required
                                        onchange="document.getElementById('dampak_masalah_label').value=this.options[this.selectedIndex].text;">
                                        <option value="">Pilih Dampak Masalah</option>
                                        <option value="20">Dampak Kecil</option>
                                        <option value="40">Dampak Sedang</option>
                                        <option value="70">Dampak Besar</option>
                                        <option value="90">Dampak Sangat Besar </option>
                                    </select>
                                    <input type="hidden" name="dampak_masalah_label" id="dampak_masalah_label">
                                </div>
                            </div>

                            {{-- Kategori Masalah --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    Kategori Masalah
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-layers"></i></span>
                                    <select class="form-control" name="kategori_masalah_skor" required
                                        onchange="document.getElementById('kategori_masalah_label').value=this.options[this.selectedIndex].text;">

                                        <option value="">Pilih Kategori Masalah</option>

                                        {{-- Akademik --}}
                                        <option value="20">Akademik</option>
                                        <option value="40">Karir</option>
                                        <option value="70">Pribadi</option>
                                        <option value="90">Sosial</option>

                                    </select>

                                    <input type="hidden" name="kategori_masalah_label" id="kategori_masalah_label">
                                </div>
                            </div>


                            {{-- Riwayat Konseling --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    Riwayat Konseling
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-clock-history"></i></span>
                                    <select class="form-control" name="riwayat_konseling_skor" required
                                        onchange="document.getElementById('riwayat_konseling_label').value=this.options[this.selectedIndex].text;">
                                        <option value="">Pilih Riwayat Konseling</option>
                                        <option value="20">Sudah Sering Konseling</option>
                                        <option value="40">Sudah Beberapa Kali </option>
                                        <option value="70">Jarang Pernah</option>
                                        <option value="90">Belum Pernah Konseling </option>
                                    </select>
                                    <input type="hidden" name="riwayat_konseling_label" id="riwayat_konseling_label">
                                </div>
                            </div>

                            {{-- Deskripsi --}}
                            <div class="mb-3">
                                <label class="form-label">Deskripsi Permasalahan</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-textarea"></i></span>
                                    <textarea class="form-control" name="deskripsi_permasalahan" rows="4" required
                                        placeholder="Jelaskan permasalahan secara lengkap..."></textarea>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Upload Bukti Masalah (Foto/Video) <span
                                        class="text-muted">(Opsional)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-paperclip"></i></span>
                                    <input type="file" class="form-control" name="bukti_masalah"
                                        accept="image/*,video/*">
                                </div>
                                <small class="text-muted">Format yang didukung: JPG, PNG, MP4, MOV. Tidak wajib.</small>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button class="btn btn-primary"><i class="bi bi-send"></i> Kirim Permohonan</button>
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
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">Tolak Permohonan Konseling</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="rejectForm" method="POST" action="{{ route('permohonan-konseling.reject', 0) }}">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label for="alasan_penolakan" class="form-label">Alasan Penolakan</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-chat-dots"></i></span>
                                    <textarea class="form-control" id="alasan_penolakan" name="alasan_penolakan"
                                        placeholder="Tuliskan alasan penolakan..." required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Tolak</button>
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

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.approve-permohonan').on('click', function() {
                $('#approveForm').attr('action', "{{ url('permohonan-konseling/approve') }}/" + $(this)
                    .data('id'));
            });

            $('.reject-permohonan').on('click', function() {
                $('#rejectForm').attr('action', "{{ url('permohonan-konseling/reject') }}/" + $(this).data(
                    'id'));
            });

            $('.complete-permohonan').on('click', function() {
                $('#completeForm').attr('action', "{{ url('permohonan-konseling/complete') }}/" + $(this)
                    .data('id'));
            });

            $('#datatablePermohonanpe').DataTable({
                order: [
                    [5, 'desc']
                ],
                columnDefs: [{
                    orderable: false,
                    targets: -1
                }]
            });
        });
    </script>
@endsection
