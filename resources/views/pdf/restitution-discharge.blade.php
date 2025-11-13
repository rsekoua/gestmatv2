<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Décharge de Restitution - {{ $attribution->numero_decharge_res }}</title>
    <style>
        @page {
            margin: 2cm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #dc2626;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #991b1b;
            margin: 0;
            font-size: 24pt;
        }
        .header .doc-number {
            color: #64748b;
            font-size: 10pt;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 10px;
            border-left: 4px solid #dc2626;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 40%;
            padding: 6px 10px;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-value {
            display: table-cell;
            padding: 6px 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 9pt;
            font-weight: bold;
        }
        .status-excellent { background-color: #d1fae5; color: #065f46; }
        .status-bon { background-color: #dbeafe; color: #1e40af; }
        .status-moyen { background-color: #fef3c7; color: #92400e; }
        .status-mauvais { background-color: #fee2e2; color: #991b1b; }
        .decision-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 10pt;
        }
        .decision-stock { background-color: #d1fae5; color: #065f46; }
        .decision-reparer { background-color: #fef3c7; color: #92400e; }
        .decision-rebut { background-color: #fee2e2; color: #991b1b; }
        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-grid {
            display: table;
            width: 100%;
        }
        .signature-cell {
            display: table-cell;
            width: 48%;
            text-align: center;
            padding: 10px;
        }
        .signature-cell.spacer {
            width: 4%;
        }
        .signature-box {
            border: 2px solid #cbd5e1;
            padding: 60px 20px 20px;
            min-height: 100px;
            margin-top: 10px;
            background-color: #fafafa;
        }
        .signature-label {
            font-weight: bold;
            color: #475569;
            margin-bottom: 10px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
            color: #94a3b8;
            padding: 10px 0;
            border-top: 1px solid #e2e8f0;
        }
        .observations {
            background-color: #f8fafc;
            padding: 15px;
            border-left: 4px solid #94a3b8;
            font-style: italic;
            min-height: 60px;
        }
        .duration-box {
            background-color: #eff6ff;
            border: 2px solid #3b82f6;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            font-weight: bold;
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DÉCHARGE DE RESTITUTION DE MATÉRIEL</h1>
        <div class="doc-number">N° {{ $attribution->numero_decharge_res }}</div>
        <div class="doc-number">Date : {{ $attribution->date_restitution->format('d/m/Y') }}</div>
    </div>

    {{-- Référence à l'attribution --}}
    <div class="section">
        <div class="section-title">RÉFÉRENCE D'ATTRIBUTION</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Numéro d'attribution</div>
                <div class="info-value"><strong>{{ $attribution->numero_decharge_att }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Date d'attribution</div>
                <div class="info-value">{{ $attribution->date_attribution->format('d/m/Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Durée d'utilisation</div>
                <div class="info-value">
                    <div class="duration-box">
                        {{ $attribution->duration_in_days }} jours
                        ({{ round($attribution->duration_in_days / 30, 1) }} mois environ)
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Informations du matériel --}}
    <div class="section">
        <div class="section-title">MATÉRIEL RESTITUÉ</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Type de matériel</div>
                <div class="info-value">{{ $attribution->materiel->materielType->nom }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Marque / Modèle</div>
                <div class="info-value">{{ $attribution->materiel->marque ?? 'N/A' }} {{ $attribution->materiel->modele ?? '' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Numéro de série</div>
                <div class="info-value"><strong>{{ $attribution->materiel->numero_serie }}</strong></div>
            </div>
        </div>
    </div>

    {{-- Informations de l'employé --}}
    <div class="section">
        <div class="section-title">EMPLOYÉ RESTITUANT</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nom complet</div>
                <div class="info-value"><strong>{{ $attribution->employee->full_name }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Service</div>
                <div class="info-value">{{ $attribution->employee->service->nom ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    {{-- État du matériel --}}
    <div class="section">
        <div class="section-title">ÉTAT DU MATÉRIEL À LA RESTITUTION</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">État général</div>
                <div class="info-value">
                    @php
                        $statusClass = match($attribution->etat_general_res) {
                            'excellent' => 'status-excellent',
                            'bon' => 'status-bon',
                            'moyen' => 'status-moyen',
                            'mauvais' => 'status-mauvais',
                            default => 'status-bon'
                        };
                    @endphp
                    <span class="status-badge {{ $statusClass }}">
                        {{ strtoupper($attribution->etat_general_res) }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">État fonctionnel</div>
                <div class="info-value">
                    @php
                        $functionalClass = match($attribution->etat_fonctionnel_res) {
                            'parfait' => 'status-excellent',
                            'defauts_mineurs' => 'status-bon',
                            'dysfonctionnements' => 'status-moyen',
                            'hors_service' => 'status-mauvais',
                            default => 'status-bon'
                        };
                        $functionalLabel = match($attribution->etat_fonctionnel_res) {
                            'parfait' => 'PARFAIT',
                            'defauts_mineurs' => 'DÉFAUTS MINEURS',
                            'dysfonctionnements' => 'DYSFONCTIONNEMENTS',
                            'hors_service' => 'HORS SERVICE',
                            default => 'N/A'
                        };
                    @endphp
                    <span class="status-badge {{ $functionalClass }}">
                        {{ $functionalLabel }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Décision</div>
                <div class="info-value">
                    @php
                        $decisionClass = match($attribution->decision_res) {
                            'remis_en_stock' => 'decision-stock',
                            'a_reparer' => 'decision-reparer',
                            'rebut' => 'decision-rebut',
                            default => 'decision-stock'
                        };
                        $decisionLabel = match($attribution->decision_res) {
                            'remis_en_stock' => '✓ REMIS EN STOCK',
                            'a_reparer' => '⚠ À RÉPARER',
                            'rebut' => '✗ REBUT',
                            default => 'N/A'
                        };
                    @endphp
                    <span class="decision-badge {{ $decisionClass }}">
                        {{ $decisionLabel }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Dommages --}}
    @if($attribution->dommages_res)
    <div class="section">
        <div class="section-title">DOMMAGES CONSTATÉS</div>
        <div class="observations" style="border-left-color: #dc2626; background-color: #fef2f2;">
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
        <div class="info-grid">
            @foreach($attribution->accessories as $accessory)
                <div class="info-row">
                    <div class="info-label">{{ $accessory->nom }}</div>
                    <div class="info-value">
                        @if($accessory->pivot->statut_res)
                            <span class="status-badge status-bon">{{ strtoupper($accessory->pivot->statut_res) }}</span>
                        @else
                            <span class="status-badge status-moyen">NON PRÉCISÉ</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Signatures --}}
    <div class="signature-section">
        <div class="section-title">SIGNATURES</div>
        <div class="signature-grid">
            <div class="signature-cell">
                <div class="signature-label">L'employé restituant</div>
                <div>{{ $attribution->employee->full_name }}</div>
                <div class="signature-box">
                    Signature et date
                </div>
            </div>
            <div class="signature-cell spacer"></div>
            <div class="signature-cell">
                <div class="signature-label">Le responsable</div>
                <div>Service Informatique</div>
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
