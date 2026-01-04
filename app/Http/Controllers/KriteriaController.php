<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\SubKriteria;
use App\Http\Requests\StoreKriteriaRequest;
use App\Http\Requests\UpdateKriteriaRequest;
use App\Http\Requests\StoreSubKriteriaRequest;
use App\Http\Requests\UpdateSubKriteriaRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
        $kriterias = Kriteria::orderBy('bobot')->get();
        $totalBobot = $kriterias->where('aktif', true)->sum('bobot');
        
        return view('admin.kriteria.index', compact('kriterias', 'totalBobot'));
    }

    public function store(StoreKriteriaRequest $request)
    {
        // Validate bobot total
        $totalBobot = Kriteria::where('aktif', true)->sum('bobot');
        
        // Add custom validation for bobot total
        $validator = Validator::make($request->all(), []);
        $validator->after(function ($validator) use ($request, $totalBobot) {
            $newTotal = round($totalBobot + (float)$request->bobot, 2);
            if ($newTotal > 1.0) {
                $validator->errors()->add('bobot', 
                    'Total bobot tidak boleh melebihi 1.0. Saat ini: ' . $totalBobot . ', Akan menjadi: ' . $newTotal);
            }
        });
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Kriteria::create($request->validated());
        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function update(UpdateKriteriaRequest $request, Kriteria $kriteria)
    {
        // Validate bobot total (excluding current kriteria)
        $otherBobot = Kriteria::where('id', '!=', $kriteria->id)
            ->where('aktif', true)
            ->sum('bobot');
        
        $validator = Validator::make($request->all(), []);
        $validator->after(function ($validator) use ($request, $otherBobot) {
            $newTotal = round($otherBobot + (float)$request->bobot, 2);
            if ($newTotal > 1.0) {
                $validator->errors()->add('bobot', 
                    'Total bobot tidak boleh melebihi 1.0. Akan menjadi: ' . $newTotal);
            }
        });
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $kriteria->update($request->validated());
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
        $subKriterias = $kriteria->subKriterias()->orderBy('skor')->get();
        return view('admin.kriteria.sub-kriteria.index', compact('kriteria', 'subKriterias'));
    }

    public function subKriteriaStore(StoreSubKriteriaRequest $request, Kriteria $kriteria)
    {
        $kriteria->subKriterias()->create($request->validated());
        return redirect()->route('kriteria.sub-kriteria.index', $kriteria)->with('success', 'Sub-kriteria berhasil ditambahkan.');
    }

    public function subKriteriaUpdate(UpdateSubKriteriaRequest $request, Kriteria $kriteria, SubKriteria $subKriteria)
    {
        $subKriteria->update($request->validated());
        return redirect()->route('kriteria.sub-kriteria.index', $kriteria)->with('success', 'Sub-kriteria berhasil diperbarui.');
    }

    public function subKriteriaDestroy(Kriteria $kriteria, SubKriteria $subKriteria)
    {
        $subKriteria->delete();
        return redirect()->route('kriteria.sub-kriteria.index', $kriteria)->with('success', 'Sub-kriteria berhasil dihapus.');
    }
}

