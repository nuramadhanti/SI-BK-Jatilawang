<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermohonanKonseling;
use App\Models\TahunAkademik;
use App\Models\Kelas;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaranList = TahunAkademik::orderBy('tahun', 'desc')->get();
        $kelasList = Kelas::with('tahunAkademik')->orderBy('nama')->get();

        $query = PermohonanKonseling::with([
            'siswa.user',
            'siswa.kelas',
            'guruBk.user'
        ])->where('status', 'selesai');

        // Filter untuk orangtua - hanya melihat laporan anaknya sendiri
        if (Auth::user()->isOrangTua()) {
            $orangtua = Auth::user()->orangtua;
            if ($orangtua && $orangtua->siswa_id) {
                $query->where('siswa_id', $orangtua->siswa_id);
            }
        }

        if ($request->tahun_akademik) {
            $query->whereHas('siswa.kelas', function ($q) use ($request) {
                $q->where('tahun_akademik_id', $request->tahun_akademik);
            });
        }

        if ($request->kelas) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas);
            });
        }

        //FILTER KATEGORI MASALAH
       if ($request->filled('kategori_masalah')) {
            $query->where('kategori_masalah_label', $request->kategori_masalah);
        }


        $laporan = $query->get();

        return view('laporan.index', [
            'laporan' => $laporan,
            'tahunAjaranList' => $tahunAjaranList,
            'kelasList' => $kelasList,
            'request' => $request
        ]);
    }

    public function cetakPdf(Request $request)
    {
        $tahunAjaranList = TahunAkademik::orderBy('tahun', 'desc')->get();

        $query = PermohonanKonseling::with([
            'siswa.user',
            'siswa.kelas'
        ])->where('status', 'selesai');

        // Filter untuk orangtua - hanya melihat laporan anaknya sendiri
        if (Auth::user()->isOrangTua()) {
            $orangtua = Auth::user()->orangtua;
            if ($orangtua && $orangtua->siswa_id) {
                $query->where('siswa_id', $orangtua->siswa_id);
            }
        }

        if ($request->tahun_akademik) {
            $query->whereHas('siswa.kelas', function ($q) use ($request) {
                $q->where('tahun_akademik_id', $request->tahun_akademik);
            });
        }

        if ($request->kelas) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas);
            });
        }
        $laporan = $query->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.pdf', [
            'laporan' => $laporan,
            'request' => $request,
            'tahunAjaranList' => $tahunAjaranList
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('laporan_konseling_' . now()->format('Ymd') . '.pdf');
    }
}
