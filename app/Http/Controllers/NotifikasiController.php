<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotifikasiController extends Controller
{
    /**
     * Display notifications list
     * PENTING: Notifikasi dipisah berdasarkan target_role (mode aktif)
     */
    public function index()
    {
        $currentMode = active_mode(); // 'karyawan' atau 'admin', 'manager', 'gm', 'hrd'

        // Query notifikasi berdasarkan mode aktif
        $notifikasi = Notifikasi::where(function($query) use ($currentMode) {
                // Notifikasi untuk user spesifik dengan role yang sesuai
                $query->where('user_id', Auth::id())
                      ->where('target_role', $currentMode);
            })
            ->orWhere(function($query) use ($currentMode) {
                // Notifikasi broadcast untuk role tertentu
                $query->whereNull('user_id')
                      ->where('target_role', $currentMode);
            })
            ->latest()
            ->paginate(20);

        return view('notifikasi.index', compact('notifikasi'));
    }

    /**
     * Mark single notification as read
     */
    public function markAsRead($id)
    {
        $currentMode = active_mode();

        $notif = Notifikasi::where('id', $id)
            ->where(function($query) use ($currentMode) {
                $query->where('user_id', Auth::id())
                      ->orWhereNull('user_id');
            })
            ->where('target_role', $currentMode)
            ->firstOrFail();

        $notif->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark multiple notifications as read (bulk)
     */
    public function markReadBulk(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:notifikasi,id'
        ]);

        $currentMode = active_mode();

        $count = Notifikasi::whereIn('id', $request->ids)
            ->where(function($query) use ($currentMode) {
                $query->where('user_id', Auth::id())
                      ->orWhereNull('user_id');
            })
            ->where('target_role', $currentMode)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => $count . ' notifikasi ditandai sebagai dibaca'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $currentMode = active_mode();

        $count = Notifikasi::where(function($query) use ($currentMode) {
                $query->where('user_id', Auth::id())
                      ->orWhereNull('user_id');
            })
            ->where('target_role', $currentMode)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => $count . ' notifikasi ditandai sebagai dibaca'
        ]);
    }

    /**
     * Delete multiple notifications (bulk)
     */
    public function deleteBulk(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:notifikasi,id'
        ]);

        $currentMode = active_mode();

        $count = Notifikasi::whereIn('id', $request->ids)
            ->where(function($query) use ($currentMode) {
                $query->where('user_id', Auth::id())
                      ->orWhereNull('user_id');
            })
            ->where('target_role', $currentMode)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => $count . ' notifikasi berhasil dihapus'
        ]);
    }

    /**
     * Delete notifications by time range
     */
    public function deleteByTime(Request $request)
    {
        $request->validate([
            'range' => 'required|in:today,week,month'
        ]);

        $currentMode = active_mode();

        $query = Notifikasi::where(function($q) use ($currentMode) {
                $q->where('user_id', Auth::id())
                  ->orWhereNull('user_id');
            })
            ->where('target_role', $currentMode);

        switch ($request->range) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->where('created_at', '>=', now()->subDays(7));
                break;
            case 'month':
                $query->where('created_at', '>=', now()->subDays(30));
                break;
        }

        $count = $query->delete();

        return response()->json([
            'success' => true,
            'message' => $count . ' notifikasi berhasil dihapus'
        ]);
    }

    /**
     * Delete all read notifications
     */
    public function deleteRead()
    {
        $currentMode = active_mode();

        $count = Notifikasi::where(function($query) use ($currentMode) {
                $query->where('user_id', Auth::id())
                      ->orWhereNull('user_id');
            })
            ->where('target_role', $currentMode)
            ->where('is_read', true)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => $count . ' notifikasi yang sudah dibaca berhasil dihapus'
        ]);
    }

    /**
     * Delete all notifications
     */
    public function deleteAll()
    {
        $currentMode = active_mode();

        $count = Notifikasi::where(function($query) use ($currentMode) {
                $query->where('user_id', Auth::id())
                      ->orWhereNull('user_id');
            })
            ->where('target_role', $currentMode)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => $count . ' notifikasi berhasil dihapus'
        ]);
    }

    /**
     * Get unread count (untuk badge di header)
     * PENTING: Hanya hitung notifikasi sesuai mode aktif
     */
    public function getUnreadCount()
    {
        $currentMode = active_mode();

        $count = Notifikasi::where(function($query) use ($currentMode) {
                $query->where('user_id', Auth::id())
                      ->orWhereNull('user_id');
            })
            ->where('target_role', $currentMode)
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
