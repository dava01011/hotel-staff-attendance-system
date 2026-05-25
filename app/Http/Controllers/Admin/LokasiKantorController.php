<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LokasiKantor;
use Illuminate\Http\Request;

class LokasiKantorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lokasiKantor = LokasiKantor::all();
        return view('admin.lokasi-kantor.index', compact('lokasiKantor'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:100|unique:lokasi_kantor,nama_lokasi',
            'latitude'    => 'required|numeric|between:-90,90',
            'longitude'   => 'required|numeric|between:-180,180',
            'radius'      => 'required|integer|min:10|max:5000',
        ], [
            'nama_lokasi.required' => 'Nama lokasi wajib diisi',
            'nama_lokasi.unique'   => 'Nama lokasi sudah ada',
            'latitude.required'    => 'Latitude wajib diisi',
            'latitude.numeric'     => 'Latitude harus berupa angka',
            'latitude.between'     => 'Latitude harus antara -90 dan 90',
            'longitude.required'   => 'Longitude wajib diisi',
            'longitude.numeric'    => 'Longitude harus berupa angka',
            'longitude.between'    => 'Longitude harus antara -180 dan 180',
            'radius.required'      => 'Radius wajib diisi',
            'radius.integer'       => 'Radius harus berupa angka',
            'radius.min'           => 'Radius minimal 10 meter',
            'radius.max'           => 'Radius maksimal 5000 meter',
        ]);

        LokasiKantor::create($validated);

        return redirect()->route('admin.lokasi-kantor.index')
                       ->with('success', 'Lokasi kantor berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LokasiKantor $lokasiKantor)
    {
        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:100|unique:lokasi_kantor,nama_lokasi,' . $lokasiKantor->id,
            'latitude'    => 'required|numeric|between:-90,90',
            'longitude'   => 'required|numeric|between:-180,180',
            'radius'      => 'required|integer|min:10|max:5000',
        ], [
            'nama_lokasi.required' => 'Nama lokasi wajib diisi',
            'nama_lokasi.unique'   => 'Nama lokasi sudah ada',
            'latitude.required'    => 'Latitude wajib diisi',
            'latitude.numeric'     => 'Latitude harus berupa angka',
            'latitude.between'     => 'Latitude harus antara -90 dan 90',
            'longitude.required'   => 'Longitude wajib diisi',
            'longitude.numeric'    => 'Longitude harus berupa angka',
            'longitude.between'    => 'Longitude harus antara -180 dan 180',
            'radius.required'      => 'Radius wajib diisi',
            'radius.integer'       => 'Radius harus berupa angka',
            'radius.min'           => 'Radius minimal 10 meter',
            'radius.max'           => 'Radius maksimal 5000 meter',
        ]);

        $lokasiKantor->update($validated);

        return redirect()->route('admin.lokasi-kantor.index')
                       ->with('success', 'Lokasi kantor berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LokasiKantor $lokasiKantor)
    {
        $lokasiKantor->delete();

        return redirect()->route('admin.lokasi-kantor.index')
                       ->with('success', 'Lokasi kantor berhasil dihapus');
    }

    /**
     * Get coordinates for Google Map
     */
    public function getCoordinates(LokasiKantor $lokasiKantor)
    {
        return response()->json([
            'id'        => $lokasiKantor->id,
            'nama'      => $lokasiKantor->nama_lokasi,
            'latitude'  => $lokasiKantor->latitude,
            'longitude' => $lokasiKantor->longitude,
            'radius'    => $lokasiKantor->radius,
        ]);
    }
}
