<?php

namespace App\Http\Controllers;

use App\Models\PermohonanKonseling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalKonselingController extends Controller
{
    public function index()
    {
        $query = PermohonanKonseling::with(['siswa.user'])
            ->where('status', 'disetujui')
            ->orderBy('skor_prioritas', 'desc')
            ->orderBy('created_at', 'asc');

        if (Auth::check()) {
            $user = Auth::user();

            switch ($user->role) {
                case 'siswa':
                    $query->where('siswa_id', $user->siswa->id);
                    break;

                case 'guru':
                    if ($user->guru && $user->guru->role_guru === 'walikelas') {
                        $query->whereHas('siswa', function ($q) use ($user) {
                            $q->where('kelas_id', $user->guru->kelas_id);
                        });

                        $siswaWali = \App\Models\Siswa::where('kelas_id', $user->guru->kelas_id)->get();
                    }
                    break;

                case 'orang_tua':
                    $query->whereHas('siswa', function ($q) use ($user) {
                        $q->where('orang_tua_id', $user->orangTua->id ?? null);
                    });
                    break;

                default:
                    break;
            }
        }

        $jadwalKonseling = $query->get();

        return view('jadwal-konseling.index', compact('jadwalKonseling'));
    }
}
