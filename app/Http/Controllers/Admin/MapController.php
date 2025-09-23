<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class MapController extends Controller
{
    public function index()
    {
        $donors = User::where('role', 'donor')
                      ->whereNotNull('latitude')
                      ->whereNotNull('longitude')
                      ->get();

        $collectors = User::where('role', 'collector')
                          ->whereNotNull('latitude')
                          ->whereNotNull('longitude')
                          ->get();

        return view('admin.modules.map', compact('donors', 'collectors'));
    }
}
