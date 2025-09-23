<?php

namespace App\Http\Controllers\Collector;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('collector.dashboard', compact('user'));
    }

    public function viewDonations()
    {
        return view('collector.donations');
    }

    public function editProfile()
    {
        $user = Auth::user();
        return view('collector.profile', compact('user'));
    }
}
