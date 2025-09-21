<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class RedirectHelper
{
    public static function redirectBasedOnRole($user)
    {
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.index');
        }

        if ($user->hasRole('client')) {
            return redirect()->route('client.index');
        }

        if ($user->hasRole('vendor')) {
            return redirect()->route('vendor.index');
        }

        // fallback if no role matched
        return redirect('/');
    }
}