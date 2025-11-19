<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Materiel;
use Illuminate\Http\Request;

class MaterielController extends Controller
{
    public function index()
    {
        return response()->json(
            Materiel::with(['materielType', 'marque'])
                ->latest()
                ->paginate(10)
        );
    }
}
