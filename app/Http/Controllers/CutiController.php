<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\User;
use App\Models\Absensi;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CutiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $karyawan = auth()->user()->karyawan;
        $cuti = Cuti::where('karyawan_id',$karyawan->id)->latest()->get();
        // return view( 'karyawan.cuti.index', compact('cuti'));
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
        $request->validate([
            'tanggal_mulai' =>'required|date',
            'tanggal_selesai' =>'required|date',
            'alasan' =>'required',
        ]);


        $karyawan = auth()->user()->karyawan;

        // $bentrok = Cuti::where('karyawan_id', $karyawan->id)
        // ->where('status', '=!', 'ditolak')
        // ->where(function ($q) use ($request){
        //     $q->whereBetWeen('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_selesai])
        //     ->orWhereBetween('tanggal_selesai', [$request->tanggal_mulai, $request->tanggal_selesai]);
        // })
        // ->first();

        $isBentrok = Absensi::where('karyawan_id', $karyawan->id)
            ->whereBetween('tanggal', [
                $request->tanggal_mulai,
                $request->tanggal_selesai
            ])->exists();

        $cuti = Cuti::create([
            'karyawan_id' => $karyawan->id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
            'status' => 'pending',
            'is_bentrok' => $isBentrok
        ]);
        Notifikasi::create([
            'user_id' => Auth::id(),
            'judul' => 'Pengajuan Cuti Dikirim',
            'pesan' => 'Pengajuan cuti Anda telah dikirim dan menunggu persetujuan.',
            'type' => 'cuti'
        ]);

        $adminIds = User::where('role', 'admin')->pluck('id');

        foreach ($adminIds as $adminId) {
            Notifikasi::create([
                'user_id' => $adminId,
                'judul' => 'Pengajuan Cuti Baru',
                'pesan' => Auth::user()->name . ' mengajukan cuti.',
                'type' => 'cuti'
            ]);
        }

        $exists = Cuti::where('karyawan_id', $karyawan->id)
            ->where('status', 'pending')
            ->where(function ($q) use ($request) {
                $q->whereBetween('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_selesai])
                ->orWhereBetween('tanggal_selesai', [$request->tanggal_mulai, $request->tanggal_selesai]);
            })->exists();

        if ($exists) {
            return back()->with('error', 'Anda sudah mengajukan cuti pada rentang tanggal ini.');
        }

        return back()->with('success', $isBentrok
                ? 'Cuti diajukan, namun terdapat bentrok jadwal. Menunggu keputusan admin.'
                : 'Cuti berhasil diajukan dan menunggu persetujuan admin.'
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
