<?php

namespace App\Http\Controllers\Donor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('donor.dashboard', compact('user'));
    }

    public function createDonation()
    {
        return view('donor.donations');
    }

    public function store()
    {
        return view('donor.store');
    }

    public function editProfile()
    {
        $user = Auth::user();
        return view('donor.profile', compact('user'));
    }
}
