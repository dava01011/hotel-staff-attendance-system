<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

if (!function_exists('active_mode')) {
    function active_mode()
    {
        if (Session::has('active_role')) {
            return Session::get('active_role');
        }

        return Auth::check() ? Auth::user()->role : null;
    }
}

if (!function_exists('is_admin_mode')) {
    function is_admin_mode()
    {
        return active_mode() === 'admin' || active_mode() === 'super_admin';
    }
}

if (!function_exists('is_karyawan_mode')) {
    function is_karyawan_mode()
    {
        return active_mode() === 'karyawan';
    }
}
