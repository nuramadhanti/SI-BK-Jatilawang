<?php

namespace App\Http\Controllers;

use App\Models\PermohonanKonseling;
use App\Models\KategoriKonseling;
use App\Models\Siswa;
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

        $query = PermohonanKonseling::with(['siswa.user'])
            ->where('status', 'menunggu')
            ->orderBy('skor_prioritas', 'desc')
            ->orderBy('created_at', 'asc');

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
        $kategoriKonseling = KategoriKonseling::all();

        return view('permohonan-konseling.index', compact(
            'permohonanKonseling',
            'kategoriKonseling',
            'siswaWali'
        ));
    }
    public function store(Request $request)
    {
        $request->validate([
            'deskripsi_permasalahan' => 'required|string',
            'tingkat_urgensi_label' => 'required|string',
            'tingkat_urgensi_skor' => 'required|integer',
            'dampak_masalah_label' => 'required|string',
            'dampak_masalah_skor' => 'required|integer',
            'kategori_masalah_label' => 'required|string',
            'kategori_masalah_skor' => 'required|integer',
            'riwayat_konseling_label' => 'required|string',
            'riwayat_konseling_skor' => 'required|integer',

            'siswa_id' => 'required_if:role,guru',
        ]);

        $user = Auth::user();

        $reportType = $user->role === 'siswa' ? 'self' : 'teacher';

        $siswaId = $user->siswa->id ?? $request->siswa_id;

        $skorAkhir =
            ($request->tingkat_urgensi_skor * 0.4) +
            ($request->dampak_masalah_skor * 0.3) +
            ($request->kategori_masalah_skor * 0.2) +
            ($request->riwayat_konseling_skor * 0.1);

        $path = null;

        if ($request->hasFile('bukti_masalah')) {
            $path = $request->file('bukti_masalah')->store('bukti-masalah', 'public');
        }

        $permohonan = PermohonanKonseling::create([
            'siswa_id' => $siswaId,
            'tanggal_pengajuan' => now(),
            'deskripsi_permasalahan' => $request->deskripsi_permasalahan,
            'bukti_masalah' => $path,
            'status' => 'menunggu',

            'report_type' => $reportType,

            'tingkat_urgensi_label' => $request->tingkat_urgensi_label,
            'tingkat_urgensi_skor' => $request->tingkat_urgensi_skor,

            'dampak_masalah_label' => $request->dampak_masalah_label,
            'dampak_masalah_skor' => $request->dampak_masalah_skor,

            'kategori_masalah_label' => $request->kategori_masalah_label,
            'kategori_masalah_skor' => $request->kategori_masalah_skor,

            'riwayat_konseling_label' => $request->riwayat_konseling_label,
            'riwayat_konseling_skor' => $request->riwayat_konseling_skor,

            'skor_prioritas' => $skorAkhir,
        ]);

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
