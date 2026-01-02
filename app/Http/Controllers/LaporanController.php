<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermohonanKonseling;
use App\Models\TahunAkademik;
use App\Models\Kelas;
use App\Models\Kriteria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaranList = TahunAkademik::orderBy('tahun', 'desc')->get();
        $kelasList = Kelas::with('tahunAkademik')->orderBy('nama')->get();

        $query = PermohonanKonseling::with([
            'siswa.user',
            'siswa.kelas',
            'guruBk.user',
            'permohonanKriterias.kriteria',
            'permohonanKriterias.subKriteria'
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

        // Analytics untuk Kriteria
        $analytics = $this->getAnalytics($laporan);

        return view('laporan.index', compact('laporan', 'tahunAjaranList', 'kelasList', 'analytics', 'request'));
    }

    private function getAnalytics($laporan)
    {
        $analytics = [
            'totalPermohonan' => $laporan->count(),
            'skorDistribusi' => [],
            'skorPerKriteria' => [],
            'permohonanTertinggi' => [],
        ];

        // Distribusi Skor
        $skorRanges = [
            'Sangat Tinggi (80-100)' => 0,
            'Tinggi (60-79)' => 0,
            'Sedang (40-59)' => 0,
            'Rendah (<40)' => 0,
        ];

        foreach ($laporan as $item) {
            if ($item->skor_prioritas >= 80) {
                $skorRanges['Sangat Tinggi (80-100)']++;
            } elseif ($item->skor_prioritas >= 60) {
                $skorRanges['Tinggi (60-79)']++;
            } elseif ($item->skor_prioritas >= 40) {
                $skorRanges['Sedang (40-59)']++;
            } else {
                $skorRanges['Rendah (<40)']++;
            }
        }

        $analytics['skorDistribusi'] = $skorRanges;

        // Rata-rata Skor per Kriteria
        $kriterias = Kriteria::where('aktif', true)->get();
        foreach ($kriterias as $kriteria) {
            $avgSkor = 0;
            $count = 0;
            
            foreach ($laporan as $item) {
                $pk = $item->permohonanKriterias->firstWhere('kriteria_id', $kriteria->id);
                if ($pk) {
                    $avgSkor += $pk->skor;
                    $count++;
                }
            }
            
            $analytics['skorPerKriteria'][$kriteria->nama] = $count > 0 ? round($avgSkor / $count, 2) : 0;
        }

        // Permohonan dengan Skor Tertinggi
        $analytics['permohonanTertinggi'] = $laporan
            ->sortByDesc('skor_prioritas')
            ->take(5)
            ->values();

        return $analytics;
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
