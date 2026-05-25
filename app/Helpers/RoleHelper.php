<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class RoleHelper
{
    /**
     * Check if user is Super Admin
     */
    public static function isSuperAdmin(): bool
    {
        return Auth::check() && Auth::user()->role === 'super_admin';
    }

    /**
     * Check if user is Admin Departemen
     */
    public static function isAdmin(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    /**
     * Check if user is Karyawan
     */
    public static function isKaryawan(): bool
    {
        return Auth::check() && Auth::user()->role === 'karyawan';
    }

    /**
     * Check if user is HRD
     */
    public static function isHRD(): bool
    {
        return Auth::check() && Auth::user()->role === 'hrd';
    }

    /**
     * Check if user is General Manager
     */
    public static function isGM(): bool
    {
        return Auth::check() && Auth::user()->role === 'gm';
    }

    /**
     * Check if user has access to menu (multiple roles)
     * @param array|string $roles
     * @return bool
     */
    public static function hasMenuAccess($roles): bool
    {
        if (!Auth::check()) {
            return false;
        }
        $userRole = Auth::user()->role;
        return in_array($userRole, (array) $roles);
    }

    // ─────────────────────────────────────────────────────────
    // MANAGE USERS (Super Admin only)
    // ─────────────────────────────────────────────────────────
    public static function canManageUsers(): bool
    {
        return self::isSuperAdmin();
    }

    // ─────────────────────────────────────────────────────────
    // KARYAWAN (View: Super Admin & Admin, CRUD: Super Admin)
    // ─────────────────────────────────────────────────────────
    public static function canViewKaryawan(): bool
    {
        return self::hasMenuAccess(['super_admin', 'admin', 'gm']);
    }

    public static function canCrudKaryawan(): bool
    {
        return self::isSuperAdmin();
    }

    /**
     * ABSENSI (View: Super Admin & Admin, CRUD: Super Admin & Admin)
     */
    public static function canViewAbsensi(): bool
    {
        return self::hasMenuAccess(['super_admin', 'admin', 'gm']);
    }

    public static function canCrudAbsensi(): bool
    {
        return self::hasMenuAccess(['super_admin', 'admin']);
    }

    // ─────────────────────────────────────────────────────────
    // CUTI (View: Super Admin & Admin, CRUD: Super Admin)
    // ─────────────────────────────────────────────────────────
    public static function canViewCuti(): bool
    {
        return self::hasMenuAccess(['super_admin', 'admin', 'gm']);
    }

    public static function canCrudCuti(): bool
    {
        return self::isSuperAdmin();
    }

    // ─────────────────────────────────────────────────────────
    // GAJI / PENGGAJIAN (Super Admin only)
    // ─────────────────────────────────────────────────────────
    public static function canManageSalary(): bool
    {
        return self::isSuperAdmin();
    }

    // ─────────────────────────────────────────────────────────
    // ACTIVITY LOG (Super Admin only)
    // ─────────────────────────────────────────────────────────
    public static function canViewActivityLog(): bool
    {
        return self::isSuperAdmin();
    }

    /**
     * APPROVAL (Super Admin & Admin)
     */
    public static function canAccessApproval(): bool
    {
        return self::hasMenuAccess(['super_admin', 'admin', 'gm']);
    }

    /**
     * SHIFT PATTERN (Super Admin & Admin)
     */
    public static function canManageShift(): bool
    {
        return self::hasMenuAccess(['super_admin', 'admin']);
    }
}