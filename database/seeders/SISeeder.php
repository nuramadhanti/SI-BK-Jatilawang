<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SISeeder extends Seeder
{
    public function run(): void
    {
        // ====== DATA DASAR ======
        $tahunAkademikId = DB::table('tahun_akademik')->insertGetId([
            'tahun' => '2024/2025',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $kelasId = DB::table('kelas')->insertGetId([
            'nama' => 'XII IPA 1',
            'tahun_akademik_id' => $tahunAkademikId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ====== SISWA (PAKAI NIS & DOMAIN SMANJA) ======
        $nis = '2024001';
        $nisn = '1234567890';
        $namaSiswa = 'Danti';
        $namaOrtu = 'Pak Andi';

        // User siswa
        $userSiswaId = DB::table('users')->insertGetId([
            'name' => $namaSiswa,
            'email' => $nis . '@smanja.sch.id',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // User orangtua
        $userOrtuId = DB::table('users')->insertGetId([
            'name' => $namaOrtu,
            'email' => 'ortu_' . $nis . '@smanja.sch.id',
            'password' => Hash::make('password'),
            'role' => 'orangtua',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Data siswa
        $siswaId = DB::table('siswa')->insertGetId([
            'user_id' => $userSiswaId,
            'nisn' => $nisn,
            'nis' => $nis,
            'kelas_id' => $kelasId,
            'jenis_kelamin' => 'P',
            'nama_orangtua' => $namaOrtu,
            'no_telp_orangtua' => '08123456789',
            'alamat' => 'Jl. Merdeka No. 10',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Data orangtua
        DB::table('orangtua')->insert([
            'user_id' => $userOrtuId,
            'nama' => $namaOrtu,
            'hubungan_dengan_siswa' => 'Ayah',
            'no_hp' => '08123456789',
            'alamat' => 'Jl. Merdeka No. 10',
            'siswa_id' => $siswaId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ====== GURU ======
        $namaGuru = 'Bu Siti';
        $userGuruId = DB::table('users')->insertGetId([
            'name' => $namaGuru,
            'email' => 'guru_' . strtolower(str_replace(' ', '', $namaGuru)) . '@smanja.sch.id',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('guru')->insert([
            'user_id' => $userGuruId,
            'nama' => $namaGuru,
            'nip' => '198001012005012001',
            'jenis_kelamin' => 'P',
            'no_hp' => '08234567890',
            'alamat' => 'Jl. Pendidikan No. 5',
            'role_guru' => 'walikelas',
            'kelas_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $namaGuru = 'Eko Adinuryadin';
        $userGuruId = DB::table('users')->insertGetId([
            'name' => $namaGuru,
            'email' => 'guru_' . strtolower(str_replace(' ', '', $namaGuru)) . '@smanja.sch.id',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('guru')->insert([
            'user_id' => $userGuruId,
            'nama' => $namaGuru,
            'nip' => '197805252008011011',
            'jenis_kelamin' => 'L',
            'no_hp' => '08234567893',
            'alamat' => 'Jl. Pendidikan No. 5',
            'role_guru' => 'kepala_sekolah',
            'kelas_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        $namaGuru = 'Suraji';
        $userGuruId = DB::table('users')->insertGetId([
            'name' => $namaGuru,
            'email' => 'guru_' . strtolower(str_replace(' ', '', $namaGuru)) . '@smanja.sch.id',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('guru')->insert([
            'user_id' => $userGuruId,
            'nama' => $namaGuru,
            'nip' => '196804102005011012',
            'jenis_kelamin' => 'L',
            'no_hp' => '08234567891',
            'alamat' => 'Jl. Pendidikan No. 5',
            'role_guru' => 'bk',
            'kelas_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ====== PERMOHONAN KONSELING (dummy) ======
        // Get guru_bk_id (Suraji)
        $guruBkId = DB::table('guru')->where('role_guru', 'bk')->first()->id;

        $permohonanId = DB::table('permohonan_konseling')->insertGetId([
            'siswa_id' => $siswaId,
            'guru_bk_id' => $guruBkId,
            'tanggal_pengajuan' => Carbon::now()->toDateString(),
            'deskripsi_permasalahan' => 'Kesulitan memahami materi Matematika dan konsentrasi saat belajar.',
            'status' => 'menunggu',
            'alasan_penolakan' => null,
            'approved_by' => null,
            'approved_at' => null,
            'rangkuman' => null,
            'tanggal_disetujui' => null,
            'tempat' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Get all kriterias for permohonan_kriteria
        $kriterias = DB::table('kriterias')->get();

        // Seeding permohonan_kriteria with sample scores
        foreach ($kriterias as $kriteria) {
            DB::table('permohonan_kriteria')->insert([
                'permohonan_konseling_id' => $permohonanId,
                'kriteria_id' => $kriteria->id,
                'skor' => rand(1, 5),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
