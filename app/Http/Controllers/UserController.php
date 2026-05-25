<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\AkunDisetujuiMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function index()
    {
        $user = User::all();
        return view('admin.user.index', compact('user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required',
            'role'     => 'required',
            'status'   => 'required',
        ]);

        try {
            User::create([
                'nama'     => $request->nama,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
                'status'   => $request->status,
            ]);

            return redirect()->back()->with('success', 'User berhasil ditambahkan');

        } catch (\Exception $e) {
            \Log::error('User Store Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

public function update(Request $request, User $user)
{
    $request->validate([
        'nama'   => 'required',
        'email'  => 'required|email|unique:users,email,' . $user->id,
        'role'   => 'required',
        'status' => 'required',
    ]);

    try {
        $data = [
            'nama'   => $request->nama,
            'email'  => $request->email,
            'role'   => $request->role,
            'status' => $request->status,
        ];

        // Jika password diisi, hash dan tambahkan ke data
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'User berhasil diperbarui');

    } catch (\Exception $e) {
        \Log::error('User Update Error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
    }
}

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        try {
            $user->delete();
            return redirect()->back()->with('success', 'User berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'User gagal dihapus');
        }
    }

    public function approval()
    {
        $users = User::where('role', 'karyawan')
                    ->where('status', 'pending')
                    ->latest()
                    ->get();

        return view('admin.user.approval', compact('users'));
    }


    public function approve(User $user)
    {
        if ($user->status !== 'pending') {
            return back()->with('error', 'User sudah diproses.');
        }

        $user->status = 'aktif';
        $user->save();

        // KIRIM EMAIL
        // Mail::to($user->email)->send(new AkunDisetujuiMail($user));

        return back()->with('success', 'Karyawan berhasil di-ACC & email dikirim.');
    }


    public function reject(User $user)
    {
        if ($user->status !== 'pending') {
            return back()->with('error', 'User sudah diproses.');
        }

        $user->delete();

        return back()->with('success', 'Pendaftaran karyawan ditolak.');
    }

}
