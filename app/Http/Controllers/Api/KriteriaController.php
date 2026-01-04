<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use Illuminate\Support\Facades\Auth;

class KriteriaController extends Controller
{
    public function index()
    {
        $kriterias = Kriteria::where('aktif', true)
            ->with(['subKriterias' => function ($query) {
                $query->where('aktif', true);
            }])
            ->get();

        // Hitung jumlah riwayat konseling yang sudah selesai untuk siswa yang login
        $jumlahRiwayatSelesai = 0;
        if (Auth::check() && Auth::user()->role === 'siswa' && Auth::user()->siswa) {
            $jumlahRiwayatSelesai = Auth::user()->siswa->getJumlahRiwayatSelesai();
        }

        return response()->json([
            'data' => $kriterias,
            'jumlah_riwayat_selesai' => $jumlahRiwayatSelesai
        ]);
    }
}
