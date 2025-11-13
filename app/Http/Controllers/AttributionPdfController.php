<?php

namespace App\Http\Controllers;

use App\Models\Attribution;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class AttributionPdfController extends Controller
{
    /**
     * Generate PDF for attribution discharge.
     */
    public function downloadAttributionDischarge(Attribution $attribution): Response
    {
        $pdf = Pdf::loadView('pdf.attribution-discharge', [
            'attribution' => $attribution,
        ]);

        $filename = "decharge_attribution_{$attribution->numero_decharge_att}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Generate PDF for restitution discharge.
     */
    public function downloadRestitutionDischarge(Attribution $attribution): Response
    {
        // Vérifier que l'attribution a bien été restituée
        if (! $attribution->isClosed()) {
            abort(404, 'Cette attribution n\'a pas encore été restituée.');
        }

        $pdf = Pdf::loadView('pdf.restitution-discharge', [
            'attribution' => $attribution,
        ]);

        $filename = "decharge_restitution_{$attribution->numero_decharge_res}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Generate combined PDF with both attribution and restitution.
     */
    public function downloadCombinedDischarge(Attribution $attribution): Response
    {
        $pdf = Pdf::loadView('pdf.combined-discharge', [
            'attribution' => $attribution,
        ]);

        $filename = "decharge_complete_{$attribution->numero_decharge_att}.pdf";

        return $pdf->download($filename);
    }
}
