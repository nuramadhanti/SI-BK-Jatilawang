<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\SubKriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KriteriaController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || Auth::user()->role !== 'guru' || !Auth::user()->guru || Auth::user()->guru->role_guru !== 'bk') {
                abort(403, 'Hanya guru BK yang dapat mengelola kriteria.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $kriterias = Kriteria::orderBy('urutan')->get();
        return view('admin.kriteria.index', compact('kriterias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|unique:kriterias,nama' . ($request->has('id') ? ',' . $request->id : ''),
            'deskripsi' => 'nullable|string',
            'bobot' => 'required|numeric|between:0,1',
            'urutan' => 'required|integer',
        ]);

        if ($request->has('id') && $request->id) {
            $kriteria = Kriteria::findOrFail($request->id);
            $kriteria->update($request->except('id'));
            return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil diperbarui.');
        }

        Kriteria::create($request->except('id'));
        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function update(Request $request, Kriteria $kriteria)
    {
        $request->validate([
            'nama' => 'required|string|unique:kriterias,nama,' . $kriteria->id,
            'deskripsi' => 'nullable|string',
            'bobot' => 'required|numeric|between:0,1',
            'urutan' => 'required|integer',
            'aktif' => 'boolean',
        ]);

        $kriteria->update($request->all());
        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil diperbarui.');
    }

    public function destroy(Kriteria $kriteria)
    {
        $kriteria->delete();
        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil dihapus.');
    }

    // Sub-Kriteria Management
    public function subKriteriaIndex(Kriteria $kriteria)
    {
        $subKriterias = $kriteria->subKriterias()->orderBy('urutan')->get();
        return view('admin.kriteria.sub-kriteria.index', compact('kriteria', 'subKriterias'));
    }

    public function subKriteriaStore(Request $request, Kriteria $kriteria)
    {
        $request->validate([
            'label' => 'required|string',
            'skor' => 'required|integer|between:0,100',
            'deskripsi' => 'nullable|string',
            'urutan' => 'required|integer',
        ]);

        if ($request->has('id') && $request->id) {
            $subKriteria = SubKriteria::findOrFail($request->id);
            $subKriteria->update($request->except('id'));
            return redirect()->route('kriteria.sub-kriteria.index', $kriteria)->with('success', 'Sub-kriteria berhasil diperbarui.');
        }

        $kriteria->subKriterias()->create($request->except('id'));
        return redirect()->route('kriteria.sub-kriteria.index', $kriteria)->with('success', 'Sub-kriteria berhasil ditambahkan.');
    }

    public function subKriteriaUpdate(Request $request, Kriteria $kriteria, SubKriteria $subKriteria)
    {
        $request->validate([
            'label' => 'required|string',
            'skor' => 'required|integer|between:0,100',
            'deskripsi' => 'nullable|string',
            'urutan' => 'required|integer',
            'aktif' => 'boolean',
        ]);

        $subKriteria->update($request->all());
        return redirect()->route('kriteria.sub-kriteria.index', $kriteria)->with('success', 'Sub-kriteria berhasil diperbarui.');
    }

    public function subKriteriaDestroy(Kriteria $kriteria, SubKriteria $subKriteria)
    {
        $subKriteria->delete();
        return redirect()->route('kriteria.sub-kriteria.index', $kriteria)->with('success', 'Sub-kriteria berhasil dihapus.');
    }
}
