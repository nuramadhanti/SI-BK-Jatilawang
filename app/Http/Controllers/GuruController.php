<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function index() //dok
    {
        $guru = Guru::with(['user', 'kelas'])->get();
        $kelas = Kelas::all();
        return view('guru.index', compact('guru', 'kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nip' => 'required|string|unique:guru,nip',
            'jenis_kelamin' => 'required|in:L,P',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'role_guru' => 'required|in:walikelas,bk,kepala_sekolah',
            'kelas_id' => 'required_if:role_guru,walikelas|nullable|exists:kelas,id',
        ]);

        // Buat user baru
        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make('password'), // Default password, bisa diganti
            'role' => 'guru',
        ]);

        // Buat data guru
        Guru::create([
            'user_id' => $user->id,
            'nama' => $request->nama,
            'nip' => $request->nip,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'role_guru' => $request->role_guru,
            'kelas_id' => $request->role_guru === 'walikelas' ? $request->kelas_id : null,
        ]);

        return redirect()->route('guru.index')->with('success', 'Guru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);
        $user = User::findOrFail($guru->user_id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'nip' => 'required|string|unique:guru,nip,' . $guru->id,
            'jenis_kelamin' => 'required|in:L,P',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'role_guru' => 'required|in:walikelas,bk,kepala_sekolah',
            'kelas_id' => 'required_if:role_guru,walikelas|nullable|exists:kelas,id',
        ]);

        // Update user
        $user->update([
            'name' => $request->nama,
            'email' => $request->email,
        ]);

        // Update guru
        $guru->update([
            'nama' => $request->nama,
            'nip' => $request->nip,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'role_guru' => $request->role_guru,
            'kelas_id' => $request->role_guru === 'walikelas' ? $request->kelas_id : null,
        ]);

        return redirect()->route('guru.index')->with('success', 'Guru berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);
        $user = User::findOrFail($guru->user_id);
        $guru->delete();
        $user->delete(); // Hapus user terkait karena onDelete('cascade')
        return redirect()->route('guru.index')->with('success', 'Guru berhasil dihapus.');
    }
}
