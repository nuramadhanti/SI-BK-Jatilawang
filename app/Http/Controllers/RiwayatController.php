<?php

namespace App\Http\Controllers;

use App\Models\PermohonanKonseling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index()
    {
        $query = PermohonanKonseling::with(['siswa.user'])
            ->whereIn('status', ['selesai', 'ditolak'])
            ->orderBy('tanggal_pengajuan', 'desc');

        if (Auth::check()) {
            $user = Auth::user();

            switch ($user->role) {
                case 'siswa':
                    $query->where('siswa_id', $user->siswa->id);
                    break;

                case 'guru':
                    if ($user->guru && $user->guru->role_guru === 'walikelas') {
                        $query->whereHas('siswa', function ($q) use ($user) {
                            $q->where('kelas_id', $user->guru->kelas_id ?? null);
                        });
                    } elseif ($user->guru && $user->guru->role_guru === 'bk') {
                    }
                    break;

                case 'orangtua':
                    $query->whereHas('siswa.orangtua', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                    break;

                default:
                    break;
            }
        }

        $riwayatKonseling = $query->get();

        return view('riwayat-konseling.index', compact('riwayatKonseling'));
    }
}
