<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Décharge Complète - {{ $attribution->numero_decharge_att }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8pt;
            color: #1f2937;
            line-height: 1.3;
        }
        .page-break {
            page-break-after: always;
        }
        .header {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 3px solid #4f46e5;
        }
        .header h1 {
            color: #4338ca;
            margin: 0 0 3px 0;
            font-size: 14pt;
            font-weight: bold;
        }
        .header-info {
            font-size: 7pt;
            color: #4b5563;
            margin: 1px 0;
        }
        .header-restitution {
            border-bottom-color: #dc2626;
        }
        .header-restitution h1 {
            color: #991b1b;
        }
        .section {
            margin-bottom: 10px;
        }
        .section-title {
            background-color: #e0e7ff;
            color: #4338ca;
            padding: 3px 6px;
            font-weight: bold;
            font-size: 8pt;
            margin-bottom: 4px;
            border-left: 3px solid #4f46e5;
        }
        .section-title-restitution {
            background-color: #fee2e2;
            color: #991b1b;
            border-left-color: #dc2626;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
        }
        td {
            padding: 3px 6px;
            border-bottom: 1px solid #e5e7eb;
        }
        .td-label {
            font-weight: bold;
            width: 35%;
            background-color: #f9fafb;
        }
        .td-value {
            width: 65%;
        }
        ul {
            margin: 0;
            padding-left: 15px;
            font-size: 7pt;
        }
        ul li {
            padding: 1px 0;
        }
        .observations {
            background-color: #f9fafb;
            padding: 6px;
            border-left: 3px solid #9ca3af;
            font-style: italic;
            font-size: 7pt;
            min-height: 25px;
        }
        .damages {
            background-color: #fef2f2;
            padding: 6px;
            border-left: 3px solid #dc2626;
            font-size: 7pt;
            min-height: 25px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 6pt;
            color: #9ca3af;
            padding-top: 6px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    {{-- PAGE 1: ATTRIBUTION --}}
    <div class="header">
        <h1>DÉCHARGE D'ATTRIBUTION</h1>
        <div class="header-info">N° {{ $attribution->numero_decharge_att }} - {{ $attribution->date_attribution->format('d/m/Y') }}</div>
    </div>

    <div class="section">
        <div class="section-title">MATÉRIEL ATTRIBUÉ</div>
        <table>
            <tr>
                <td class="td-label">Type</td>
                <td class="td-value">{{ $attribution->materiel->materielType->nom }}</td>
            </tr>
            <tr>
                <td class="td-label">Marque / Modèle</td>
                <td class="td-value">{{ $attribution->materiel->marque }} {{ $attribution->materiel->modele }}</td>
            </tr>
            <tr>
                <td class="td-label">N° Série</td>
                <td class="td-value"><strong>{{ $attribution->materiel->numero_serie }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">BÉNÉFICIAIRE</div>
        <table>
            <tr>
                <td class="td-label">Nom</td>
                <td class="td-value">{{ $attribution->employee->full_name }}</td>
            </tr>
            <tr>
                <td class="td-label">Service</td>
                <td class="td-value">{{ $attribution->employee->service->nom ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    @if($attribution->accessories->count() > 0)
    <div class="section">
        <div class="section-title">ACCESSOIRES</div>
        <ul>
            @foreach($attribution->accessories as $accessory)
                <li>{{ $accessory->nom }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($attribution->observations_att)
    <div class="section">
        <div class="section-title">OBSERVATIONS</div>
        <div class="observations">
            {{ $attribution->observations_att }}
        </div>
    </div>
    @endif

    {{-- PAGE 2: RESTITUTION (si applicable) --}}
    @if($attribution->isClosed())
        <div class="page-break"></div>

        <div class="header header-restitution">
            <h1>DÉCHARGE DE RESTITUTION</h1>
            <div class="header-info">N° {{ $attribution->numero_decharge_res }} - {{ $attribution->date_restitution->format('d/m/Y') }}</div>
        </div>

        <div class="section">
            <div class="section-title section-title-restitution">RÉFÉRENCE</div>
            <table>
                <tr>
                    <td class="td-label">Attribution</td>
                    <td class="td-value">{{ $attribution->numero_decharge_att }}</td>
                </tr>
                <tr>
                    <td class="td-label">Durée</td>
                    <td class="td-value">{{ $attribution->duration_in_days }} jours</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title section-title-restitution">ÉTAT DU MATÉRIEL</div>
            <table>
                <tr>
                    <td class="td-label">État général</td>
                    <td class="td-value">{{ strtoupper($attribution->etat_general_res) }}</td>
                </tr>
                <tr>
                    <td class="td-label">État fonctionnel</td>
                    <td class="td-value">
                        @php
                            $functionalLabel = match($attribution->etat_fonctionnel_res) {
                                'parfait' => 'PARFAIT',
                                'defauts_mineurs' => 'DÉFAUTS MINEURS',
                                'dysfonctionnements' => 'DYSFONCTIONNEMENTS',
                                'hors_service' => 'HORS SERVICE',
                                default => 'N/A'
                            };
                        @endphp
                        {{ $functionalLabel }}
                    </td>
                </tr>
                <tr>
                    <td class="td-label">Décision</td>
                    <td class="td-value">
                        @php
                            $decisionLabel = match($attribution->decision_res) {
                                'remis_en_stock' => 'REMIS EN STOCK',
                                'a_reparer' => 'À RÉPARER',
                                'rebut' => 'REBUT',
                                default => 'N/A'
                            };
                        @endphp
                        <strong>{{ $decisionLabel }}</strong>
                    </td>
                </tr>
            </table>
        </div>

        @if($attribution->dommages_res)
        <div class="section">
            <div class="section-title section-title-restitution">DOMMAGES CONSTATÉS</div>
            <div class="damages">
                {{ $attribution->dommages_res }}
            </div>
        </div>
        @endif

        @if($attribution->observations_res)
        <div class="section">
            <div class="section-title section-title-restitution">OBSERVATIONS</div>
            <div class="observations">
                {{ $attribution->observations_res }}
            </div>
        </div>
        @endif
    @endif

    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }} - Décharge complète {{ $attribution->numero_decharge_att }}
    </div>
</body>
</html>
