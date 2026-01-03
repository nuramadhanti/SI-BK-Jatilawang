<?php

namespace App\Http\Controllers;

use App\Models\PermohonanKonseling;
use App\Models\PermohonanKriteria;
use App\Models\Kriteria;
use App\Models\Siswa;
use App\Http\Requests\StorePermohonanKonselingRequest;
use App\Notifications\PermohonanKonselingNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

use App\Models\User;

class PermohonanKonselingController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        $user = Auth::user();

        $query = PermohonanKonseling::with(['siswa.user', 'permohonanKriterias.kriteria', 'permohonanKriterias.subKriteria'])
            ->where('status', 'menunggu')
            ->orderBy('skor_prioritas', 'desc')
            ->orderBy('created_at', 'desc');

        $siswaWali = collect();

        switch ($user->role) {
            case 'siswa':
                $query->where('siswa_id', $user->siswa->id);
                break;

            case 'guru':
                if ($user->guru && $user->guru->role_guru === 'walikelas') {
                    $query->whereHas('siswa', function ($q) use ($user) {
                        $q->where('kelas_id', $user->guru->kelas_id);
                    });

                    $siswaWali = Siswa::where('kelas_id', $user->guru->kelas_id)->get();
                }
                break;

            default:
                abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $permohonanKonseling = $query->get();
        $kriterias = Kriteria::where('aktif', true)->orderBy('urutan')->get();

        return view('permohonan-konseling.index', compact(
            'permohonanKonseling',
            'siswaWali',
            'kriterias'
        ));
    }
    public function store(StorePermohonanKonselingRequest $request)
    {
        $user = Auth::user();

        $reportType = $user->role === 'siswa' ? 'self' : 'teacher';

        $siswaId = $user->siswa->id ?? $request->siswa_id;

        $path = null;

        if ($request->hasFile('bukti_masalah')) {
            $path = $request->file('bukti_masalah')->store('bukti-masalah', 'public');
        }

        // Collect kriteria dari request (radio buttons dengan format sub_kriteria_{id})
        $kriteriaSubmitted = [];
        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'sub_kriteria_') === 0 && !empty($value)) {
                $kriteriaSubmitted[] = $value;
            }
        }

        if (empty($kriteriaSubmitted)) {
            return redirect()->back()->withErrors('Pilih minimal satu kriteria untuk setiap kategori.');
        }

        // AUTO-DETECT: Ambil sub-kriteria riwayat konseling dari history
        $siswa = Siswa::findOrFail($siswaId);
        $subKriteriaRiwayatOtomatis = $siswa->getSubKriteriaRiwayatOtomatis();
        
        if ($subKriteriaRiwayatOtomatis) {
            // Tambahkan otomatis ke kriteria yang dipilih
            $kriteriaSubmitted[] = $subKriteriaRiwayatOtomatis->id;
        }

        // Get all kriteria
        $allKriterias = Kriteria::with('subKriterias')->get();
        
        /**
         * PERHITUNGAN SKOR AKHIR
         * RUMUS: Skor Akhir = (k1 × bobot) + (k2 × bobot) + (k3 × bobot) + (k4 × bobot) + ...
         * 
         * Dimana:
         * - k1, k2, k3, dst = skor sub-kriteria yang dipilih user
         * - bobot = bobot kriteria dari database (0-1)
         * 
         * Iterasi setiap sub-kriteria yang dipilih, ambil scorenya,
         * kalikan dengan bobot kriteria, lalu jumlahkan semuanya
         */
        $skorAkhir = 0;
        $kriteriaBreakdown = []; // Untuk tracking detail perhitungan

        // Hitung skor akhir dan siapkan data
        $permohonanKriteriaData = [];
        foreach ($kriteriaSubmitted as $subKriteriaId) {
            $subKriteria = \App\Models\SubKriteria::with('kriteria')->findOrFail($subKriteriaId);
            $skor = $subKriteria->skor;
            $bobot = $subKriteria->kriteria->bobot;
            $skorTerbobot = $skor * $bobot;
            
            // Accumulate score: Skor Akhir += (skor × bobot)
            $skorAkhir += $skorTerbobot;
            
            // Log breakdown untuk audit trail
            $kriteriaBreakdown[] = [
                'kriteria' => $subKriteria->kriteria->nama,
                'skor' => $skor,
                'bobot' => $bobot,
                'hasil' => $skorTerbobot
            ];

            $permohonanKriteriaData[] = [
                'kriteria_id' => $subKriteria->kriteria_id,
                'sub_kriteria_id' => $subKriteria->id,
                'skor' => $skor,
            ];
        }

        // Round final score ke 2 desimal
        $skorAkhir = round($skorAkhir, 2);

        // Create permohonan
        $permohonan = PermohonanKonseling::create([
            'siswa_id' => $siswaId,
            'tanggal_pengajuan' => now(),
            'deskripsi_permasalahan' => $request->deskripsi_permasalahan,
            'bukti_masalah' => $path,
            'status' => 'menunggu',
            'report_type' => $reportType,
            'skor_prioritas' => $skorAkhir,
        ]);

        // Simpan kriteria yang dipilih
        foreach ($permohonanKriteriaData as $data) {
            PermohonanKriteria::create([
                'permohonan_konseling_id' => $permohonan->id,
                'kriteria_id' => $data['kriteria_id'],
                'sub_kriteria_id' => $data['sub_kriteria_id'],
                'skor' => $data['skor'],
            ]);
        }

        $guruBk = User::whereHas('guru', fn($q) => $q->where('role_guru', 'bk'))->get();
        $pengaju = $user->name;

        foreach ($guruBk as $guru) {
            $guru->notify(new PermohonanKonselingNotification(
                $permohonan,
                "$pengaju mengajukan permohonan konseling."
            ));
        }

        return redirect()->back()->with('success', 'Permohonan konseling berhasil diajukan.');
    }

    public function updateJadwal(Request $request, $id)
    {
        $request->validate([
            'tanggal_disetujui' => 'required|date',
            'tempat' => 'required|string|max:255',
        ]);

        $jadwal = PermohonanKonseling::findOrFail($id);

        $jadwal->update([
            'tanggal_disetujui' => $request->tanggal_disetujui,
            'tempat' => $request->tempat,
        ]);

        return back()->with('success', 'Jadwal konseling berhasil diperbarui.');
    }



    public function approve(Request $request, $id)
    {
        if (Auth::user()->role !== 'guru' || !Auth::user()->guru || Auth::user()->guru->role_guru !== 'bk') {
            return redirect()->back()->with('error', 'Hanya guru BK yang dapat menyetujui permohonan.');
        }

        $request->validate([
            'tanggal_disetujui' => 'required|date',
            'tempat' => 'required|string|max:255',
        ]);

        $permohonan = PermohonanKonseling::findOrFail($id);
        $permohonan->update([
            'status' => 'disetujui',
            'tanggal_disetujui' => $request->tanggal_disetujui,
            'tempat' => $request->tempat,
            'nama_konselor' => Auth::user()->name,
        ]);

        $user = $permohonan->siswa->user;
        Notification::send($user, new PermohonanKonselingNotification($permohonan, 'Permohonan konseling Anda telah disetujui.'));

        return redirect()->back()->with('success', 'Permohonan konseling berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        if (Auth::user()->role !== 'guru' || !Auth::user()->guru || Auth::user()->guru->role_guru !== 'bk') {
            return redirect()->back()->with('error', 'Hanya guru BK yang dapat menolak permohonan.');
        }

        $permohonan = PermohonanKonseling::findOrFail($id);
        $permohonan->update([
            'status' => 'ditolak',
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);

        $user = $permohonan->siswa->user;
        Notification::send($user, new PermohonanKonselingNotification($permohonan, 'Permohonan konseling Anda telah ditolak.'));

        return redirect()->back()->with('success', 'Permohonan konseling berhasil ditolak.');
    }

    public function complete(Request $request, $id)
    {
        if (Auth::user()->role !== 'guru' || !Auth::user()->guru || Auth::user()->guru->role_guru !== 'bk') {
            return redirect()->back()->with('error', 'Hanya guru BK yang dapat menyelesaikan permohonan.');
        }

        $request->validate([
            'rangkuman' => 'required|string',
        ]);

        $permohonan = PermohonanKonseling::findOrFail($id);
        $permohonan->update([
            'status' => 'selesai',
            'rangkuman' => $request->rangkuman,
            'nama_konselor' => Auth::user()->name,
        ]);

        // Kirim notifikasi ke siswa
        $user = $permohonan->siswa->user;
        Notification::send($user, new PermohonanKonselingNotification($permohonan, 'Permohonan konseling Anda telah selesai.'));

        return redirect()->back()->with('success', 'Permohonan konseling telah selesai.');
    }
}
