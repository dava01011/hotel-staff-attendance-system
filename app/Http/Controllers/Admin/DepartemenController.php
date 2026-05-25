<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Departemen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DepartemenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departemen = Departemen::all();
        return view('admin.departemen.index', compact('departemen'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
        $request->validate([
            'nama'   => 'required',

        ]);

        Departemen::create([
            'nama'   => $request->nama,
        ]);

            return redirect()->back()->with('success', 'Departemen berhasil ditambahkan');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan departemen!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Departemen $id)
    {
    try {
        $request->validate([
            'nama'   => 'required',

        ]);

        $departemen->update([
            'nama'   => $request->nama,
        ]);


            return redirect()->back()->with('success', 'Departemen berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui departemen!');
        }    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Departemen $id)
    {
        try {
            $departemen->delete();
            return redirect()->back()->with('success', 'Departemen berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Departemen gagal dihapus');
        }
    }
}
