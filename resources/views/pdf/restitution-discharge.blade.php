<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Décharge de Restitution - {{ $attribution->numero_decharge_res }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9pt;
            color: #1f2937;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 4px solid #dc2626;
        }
        .header h1 {
            color: #991b1b;
            margin: 0 0 5px 0;
            font-size: 16pt;
            font-weight: bold;
        }
        .header-info {
            font-size: 8pt;
            color: #4b5563;
            margin: 2px 0;
        }
        .section {
            margin-bottom: 12px;
        }
        .section-title {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 5px;
            border-left: 4px solid #dc2626;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }
        td {
            padding: 4px 8px;
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
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 7pt;
            font-weight: bold;
        }
        .badge-green { background-color: #d1fae5; color: #065f46; }
        .badge-blue { background-color: #dbeafe; color: #1e40af; }
        .badge-yellow { background-color: #fef3c7; color: #92400e; }
        .badge-red { background-color: #fee2e2; color: #991b1b; }
        .observations {
            background-color: #f9fafb;
            padding: 8px;
            border-left: 4px solid #9ca3af;
            font-style: italic;
            font-size: 8pt;
            min-height: 30px;
        }
        .damages {
            background-color: #fef2f2;
            padding: 8px;
            border-left: 4px solid #dc2626;
            font-size: 8pt;
            min-height: 30px;
        }
        .signature-section {
            margin-top: 20px;
        }
        .signature-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        .signature-cell {
            display: table-cell;
            width: 45%;
            text-align: center;
            vertical-align: top;
        }
        .signature-spacer {
            display: table-cell;
            width: 10%;
        }
        .signature-label {
            font-weight: bold;
            color: #374151;
            margin-bottom: 5px;
            font-size: 8pt;
        }
        .signature-name {
            margin-bottom: 5px;
            font-size: 8pt;
        }
        .signature-box {
            border: 2px solid #d1d5db;
            background-color: #f9fafb;
            height: 60px;
            padding-top: 40px;
            font-size: 8pt;
            color: #6b7280;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7pt;
            color: #9ca3af;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DÉCHARGE DE RESTITUTION DE MATÉRIEL</h1>
        <div class="header-info">N° {{ $attribution->numero_decharge_res }}</div>
        <div class="header-info">Date : {{ $attribution->date_restitution->format('d/m/Y') }}</div>
    </div>

    {{-- Référence à l'attribution --}}
    <div class="section">
        <div class="section-title">RÉFÉRENCE D'ATTRIBUTION</div>
        <table>
            <tr>
                <td class="td-label">Numéro d'attribution</td>
                <td class="td-value"><strong>{{ $attribution->numero_decharge_att }}</strong></td>
            </tr>
            <tr>
                <td class="td-label">Date d'attribution</td>
                <td class="td-value">{{ $attribution->date_attribution->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="td-label">Durée d'utilisation</td>
                <td class="td-value">
                    <span class="badge badge-blue">
                        {{ $attribution->duration_in_days }} jours ({{ round($attribution->duration_in_days / 30, 1) }} mois)
                    </span>
                </td>
            </tr>
        </table>
    </div>

    {{-- Informations du matériel --}}
    <div class="section">
        <div class="section-title">MATÉRIEL RESTITUÉ</div>
        <table>
            <tr>
                <td class="td-label">Type de matériel</td>
                <td class="td-value">{{ $attribution->materiel->materielType->nom }}</td>
            </tr>
            <tr>
                <td class="td-label">Marque / Modèle</td>
                <td class="td-value">{{ $attribution->materiel->marque ?? 'N/A' }} {{ $attribution->materiel->modele ?? '' }}</td>
            </tr>
            <tr>
                <td class="td-label">Numéro de série</td>
                <td class="td-value"><strong>{{ $attribution->materiel->numero_serie }}</strong></td>
            </tr>
        </table>
    </div>

    {{-- Informations de l'employé ou du service --}}
    <div class="section">
        <div class="section-title">{{ $attribution->isForEmployee() ? 'EMPLOYÉ RESTITUANT' : 'SERVICE RESTITUANT' }}</div>
        <table>
            @if($attribution->isForEmployee())
                <tr>
                    <td class="td-label">Nom complet</td>
                    <td class="td-value"><strong>{{ $attribution->employee->full_name }}</strong></td>
                </tr>
                <tr>
                    <td class="td-label">Service</td>
                    <td class="td-value">{{ $attribution->employee->service->nom ?? 'N/A' }}</td>
                </tr>
            @else
                <tr>
                    <td class="td-label">Service</td>
                    <td class="td-value"><strong>{{ $attribution->service->nom }}</strong></td>
                </tr>
                @if($attribution->service->responsable)
                <tr>
                    <td class="td-label">Chef de service</td>
                    <td class="td-value"><strong>{{ $attribution->service->responsable }}</strong></td>
                </tr>
                @endif
            @endif
        </table>
    </div>

    {{-- État du matériel --}}
    <div class="section">
        <div class="section-title">ÉTAT DU MATÉRIEL À LA RESTITUTION</div>
        <table>
            <tr>
                <td class="td-label">État général</td>
                <td class="td-value">
                    @php
                        $statusClass = match($attribution->etat_general_res) {
                            'excellent' => 'badge-green',
                            'bon' => 'badge-blue',
                            'moyen' => 'badge-yellow',
                            'mauvais' => 'badge-red',
                            default => 'badge-blue'
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}">
                        {{ strtoupper($attribution->etat_general_res) }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="td-label">État fonctionnel</td>
                <td class="td-value">
                    @php
                        $functionalClass = match($attribution->etat_fonctionnel_res) {
                            'parfait' => 'badge-green',
                            'defauts_mineurs' => 'badge-blue',
                            'dysfonctionnements' => 'badge-yellow',
                            'hors_service' => 'badge-red',
                            default => 'badge-blue'
                        };
                        $functionalLabel = match($attribution->etat_fonctionnel_res) {
                            'parfait' => 'PARFAIT',
                            'defauts_mineurs' => 'DÉFAUTS MINEURS',
                            'dysfonctionnements' => 'DYSFONCTIONNEMENTS',
                            'hors_service' => 'HORS SERVICE',
                            default => 'N/A'
                        };
                    @endphp
                    <span class="badge {{ $functionalClass }}">
                        {{ $functionalLabel }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="td-label">Décision</td>
                <td class="td-value">
                    @php
                        $decisionClass = match($attribution->decision_res) {
                            'remis_en_stock' => 'badge-green',
                            'a_reparer' => 'badge-yellow',
                            'rebut' => 'badge-red',
                            default => 'badge-blue'
                        };
                        $decisionLabel = match($attribution->decision_res) {
                            'remis_en_stock' => '✓ REMIS EN STOCK',
                            'a_reparer' => '⚠ À RÉPARER',
                            'rebut' => '✗ REBUT',
                            default => 'N/A'
                        };
                    @endphp
                    <span class="badge {{ $decisionClass }}">
                        {{ $decisionLabel }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    {{-- Dommages --}}
    @if($attribution->dommages_res)
    <div class="section">
        <div class="section-title">DOMMAGES CONSTATÉS</div>
        <div class="damages">
            {{ $attribution->dommages_res }}
        </div>
    </div>
    @endif

    {{-- Observations --}}
    @if($attribution->observations_res)
    <div class="section">
        <div class="section-title">OBSERVATIONS</div>
        <div class="observations">
            {{ $attribution->observations_res }}
        </div>
    </div>
    @endif

    {{-- Accessoires restitués --}}
    @if($attribution->accessories->count() > 0)
    <div class="section">
        <div class="section-title">ACCESSOIRES RESTITUÉS</div>
        <table>
            @foreach($attribution->accessories as $accessory)
                <tr>
                    <td class="td-label" style="width: 60%;">{{ $accessory->nom }}</td>
                    <td class="td-value" style="width: 40%;">
                        @if($accessory->pivot->statut_res)
                            <span class="badge badge-blue">{{ strtoupper($accessory->pivot->statut_res) }}</span>
                        @else
                            <span class="badge badge-yellow">NON PRÉCISÉ</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
    @endif

    {{-- Signatures --}}
    <div class="signature-section">
        <div class="section-title">SIGNATURES</div>
        <div class="signature-grid">
            <div class="signature-cell">
                @if($attribution->isForEmployee())
                    <div class="signature-label">L'employé restituant</div>
                    <div class="signature-name">{{ $attribution->employee->full_name }}</div>
                @else
                    <div class="signature-label">Le chef de service</div>
                    <div class="signature-name">{{ $attribution->service->responsable ?? 'N/A' }}</div>
                @endif
                <div class="signature-box">
                    Signature et date
                </div>
            </div>
            <div class="signature-spacer"></div>
            <div class="signature-cell">
                <div class="signature-label">Le responsable</div>
                <div class="signature-name">Service Informatique</div>
                <div class="signature-box">
                    Signature et cachet
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }} - Décharge de restitution {{ $attribution->numero_decharge_res }}
    </div>
</body>
</html>
