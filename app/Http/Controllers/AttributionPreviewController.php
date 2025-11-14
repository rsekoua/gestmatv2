<?php

namespace App\Http\Controllers;

use App\Models\Attribution;
use Illuminate\View\View;

class AttributionPreviewController extends Controller
{
    /**
     * Preview attribution discharge
     */
    public function previewAttribution(Attribution $attribution): View
    {
        $attribution->load([
            'materiel.materielType',
            'employee.service',
            'service',
            'accessories',
        ]);

        return view('attributions.preview-attribution', compact('attribution'));
    }

    /**
     * Preview restitution discharge
     */
    public function previewRestitution(Attribution $attribution): View
    {
        $attribution->load([
            'materiel.materielType',
            'employee.service',
            'service',
            'accessories',
        ]);

        return view('attributions.preview-restitution', compact('attribution'));
    }
}
