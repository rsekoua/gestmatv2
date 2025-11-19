<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attribution;
use App\Models\Employee;
use App\Models\Materiel;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats()
    {
        return response()->json([
            'total_materials' => Materiel::count(),
            'total_employees' => Employee::count(),
            'active_attributions' => Attribution::whereNull('date_restitution')->count(),
            'recent_attributions' => Attribution::with(['employee', 'materiel'])
                ->latest()
                ->take(5)
                ->get()
        ]);
    }
}
