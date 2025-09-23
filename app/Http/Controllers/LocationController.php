<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    public function getDepartments()
    {
        $departments = Location::select('department')->distinct()->orderBy('department')->get();
        return response()->json($departments);
    }

    public function getProvinces(Request $request)
    {
        $provinces = Location::where('department', $request->department)
            ->select('province')
            ->distinct()
            ->orderBy('province')
            ->get();

        return response()->json($provinces);
    }

    public function getMunicipalities(Request $request)
    {
        $municipalities = Location::where('department', $request->department)
            ->where('province', $request->province)
            ->select('municipality')
            ->distinct()
            ->orderBy('municipality')
            ->get();

        return response()->json($municipalities);
    }
}
