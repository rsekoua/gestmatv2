<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Décharge Complète - {{ $attribution->numero_decharge_att }}</title>
    <style>
        @page {
            margin: 2cm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.5;
        }
        .page-break {
            page-break-after: always;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #6366f1;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #4338ca;
            margin: 0;
            font-size: 20pt;
        }
        .header .subtitle {
            color: #64748b;
            font-size: 10pt;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            background-color: #e0e7ff;
            color: #4338ca;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 8px;
            border-left: 4px solid #6366f1;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 35%;
            padding: 5px 8px;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9pt;
        }
        .info-value {
            display: table-cell;
            padding: 5px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9pt;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #94a3b8;
            padding: 10px 0;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    {{-- PAGE 1: ATTRIBUTION --}}
    <div class="header">
        <h1>DÉCHARGE D'ATTRIBUTION</h1>
        <div class="subtitle">N° {{ $attribution->numero_decharge_att }} - {{ $attribution->date_attribution->format('d/m/Y') }}</div>
    </div>

    <div class="section">
        <div class="section-title">MATÉRIEL ATTRIBUÉ</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Type</div>
                <div class="info-value">{{ $attribution->materiel->materielType->nom }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Marque / Modèle</div>
                <div class="info-value">{{ $attribution->materiel->marque }} {{ $attribution->materiel->modele }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">N° Série</div>
                <div class="info-value"><strong>{{ $attribution->materiel->numero_serie }}</strong></div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">BÉNÉFICIAIRE</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nom</div>
                <div class="info-value">{{ $attribution->employee->full_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Service</div>
                <div class="info-value">{{ $attribution->employee->service->nom ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    @if($attribution->accessories->count() > 0)
    <div class="section">
        <div class="section-title">ACCESSOIRES</div>
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($attribution->accessories as $accessory)
                <li style="padding: 3px 0;">{{ $accessory->nom }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($attribution->observations_att)
    <div class="section">
        <div class="section-title">OBSERVATIONS</div>
        <div style="background-color: #f8fafc; padding: 10px; font-style: italic;">
            {{ $attribution->observations_att }}
        </div>
    </div>
    @endif

    {{-- PAGE 2: RESTITUTION (si applicable) --}}
    @if($attribution->isClosed())
        <div class="page-break"></div>

        <div class="header">
            <h1>DÉCHARGE DE RESTITUTION</h1>
            <div class="subtitle">N° {{ $attribution->numero_decharge_res }} - {{ $attribution->date_restitution->format('d/m/Y') }}</div>
        </div>

        <div class="section">
            <div class="section-title">RÉFÉRENCE</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Attribution</div>
                    <div class="info-value">{{ $attribution->numero_decharge_att }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Durée</div>
                    <div class="info-value">{{ $attribution->duration_in_days }} jours</div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">ÉTAT DU MATÉRIEL</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">État général</div>
                    <div class="info-value">{{ strtoupper($attribution->etat_general_res) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">État fonctionnel</div>
                    <div class="info-value">
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
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Décision</div>
                    <div class="info-value">
                        @php
                            $decisionLabel = match($attribution->decision_res) {
                                'remis_en_stock' => 'REMIS EN STOCK',
                                'a_reparer' => 'À RÉPARER',
                                'rebut' => 'REBUT',
                                default => 'N/A'
                            };
                        @endphp
                        <strong>{{ $decisionLabel }}</strong>
                    </div>
                </div>
            </div>
        </div>

        @if($attribution->dommages_res)
        <div class="section">
            <div class="section-title">DOMMAGES CONSTATÉS</div>
            <div style="background-color: #fef2f2; padding: 10px; border-left: 4px solid #dc2626;">
                {{ $attribution->dommages_res }}
            </div>
        </div>
        @endif

        @if($attribution->observations_res)
        <div class="section">
            <div class="section-title">OBSERVATIONS</div>
            <div style="background-color: #f8fafc; padding: 10px; font-style: italic;">
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
