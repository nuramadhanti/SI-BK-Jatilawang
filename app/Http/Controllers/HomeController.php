<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // Dashboard untuk Siswa
        if ($user->role === 'siswa') {
            $siswa = $user->siswa;
            
            // Jadwal konseling yang sudah disetujui
            $jadwalKonseling = \App\Models\PermohonanKonseling::where('siswa_id', $siswa->id)
                ->where('status', 'disetujui')
                ->orderBy('tanggal_disetujui', 'desc')
                ->take(5)
                ->get();
            
            // Riwayat permohonan konseling (semua status)
            $permohonanKonseling = \App\Models\PermohonanKonseling::where('siswa_id', $siswa->id)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
            
            // Statistik
            $totalKonseling = \App\Models\PermohonanKonseling::where('siswa_id', $siswa->id)
                ->where('status', 'disetujui')
                ->count();
            
            $permohonanPending = \App\Models\PermohonanKonseling::where('siswa_id', $siswa->id)
                ->where('status', 'menunggu')
                ->count();
            
            $konselingSelesai = \App\Models\PermohonanKonseling::where('siswa_id', $siswa->id)
                ->where('status', 'selesai')
                ->count();
            
            $guruBk = \App\Models\Guru::where('role_guru', 'bk')->first();
            
            return view('home', compact('user', 'siswa', 'jadwalKonseling', 'permohonanKonseling', 'totalKonseling', 'permohonanPending', 'konselingSelesai', 'guruBk'));
        }
        
        // Dashboard untuk Orang Tua
        if ($user->role === 'orangtua') {
            $orangtua = $user->orangtua;
            
            // Ambil data siswa/anak yang terkait
            $anak = $orangtua ? $orangtua->siswa : null;
            
            if ($anak) {
                // Jadwal konseling anak
                $jadwalKonseling = \App\Models\PermohonanKonseling::where('siswa_id', $anak->id)
                    ->where('status', 'disetujui')
                    ->orderBy('tanggal_disetujui', 'desc')
                    ->take(5)
                    ->get();
                
                // Permohonan konseling anak
                $permohonanKonseling = \App\Models\PermohonanKonseling::where('siswa_id', $anak->id)
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get();
                
                // Statistik anak
                $totalKonseling = \App\Models\PermohonanKonseling::where('siswa_id', $anak->id)
                    ->where('status', 'disetujui')
                    ->count();
                
                $permohonanPending = \App\Models\PermohonanKonseling::where('siswa_id', $anak->id)
                    ->where('status', 'menunggu')
                    ->count();
                
                $konselingSelesai = \App\Models\PermohonanKonseling::where('siswa_id', $anak->id)
                    ->where('status', 'selesai')
                    ->count();
                
                $guruBk = \App\Models\Guru::where('role_guru', 'bk')->first();
            } else {
                $jadwalKonseling = collect();
                $permohonanKonseling = collect();
                $totalKonseling = 0;
                $permohonanPending = 0;
                $konselingSelesai = 0;
                $guruBk = null;
            }
            
            return view('home', compact('user', 'orangtua', 'anak', 'jadwalKonseling', 'permohonanKonseling', 'totalKonseling', 'permohonanPending', 'konselingSelesai', 'guruBk'));
        }
        
        // Dashboard untuk Guru/Admin
        $countSiswa = \App\Models\User::where('role', 'siswa')->count();
        $countOrtu = \App\Models\User::where('role', 'orangtua')->count();
        $countGuru = \App\Models\User::where('role', 'guru')->count();
        $countWalikelas = \App\Models\Guru::where('role_guru', 'walikelas')->count();
        $countGuruBk = \App\Models\Guru::where('role_guru', 'bk')->count();
        
        return view('home', compact('countSiswa', 'countOrtu', 'countGuru', 'countWalikelas', 'countGuruBk', 'user'));
    }
}
