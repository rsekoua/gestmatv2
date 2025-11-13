<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Décharge d'Attribution - {{ $attribution->numero_decharge_att }}</title>
    <style>
        @page {
            margin: 1cm 2cm;
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
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #1e40af;
            margin: 0;
            font-size: 18pt;
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
            background-color: #dbeafe;
            color: #1e40af;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 10px;
            border-left: 4px solid #2563eb;
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
        .accessories-list {
            list-style: none;
            padding-left: 0;
        }
        .accessories-list li {
            padding: 5px 0;
            border-bottom: 1px dashed #e2e8f0;
        }
        .accessories-list li:before {
            content: "✓ ";
            color: #10b981;
            font-weight: bold;
            margin-right: 5px;
        }
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
    </style>
</head>
<body>
    <div class="header">
        <h1>DÉCHARGE POUR ATTRIBUTION DE MATÉRIEL</h1>
        <div class="doc-number">N° {{ $attribution->numero_decharge_att }}</div>
        <div class="doc-number">Date : {{ $attribution->date_attribution->format('d/m/Y') }}</div>
    </div>

    {{-- Informations du matériel --}}
    <div class="section">
        <div class="section-title">INFORMATIONS DU MATÉRIEL</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Type de matériel</div>
                <div class="info-value">{{ $attribution->materiel->materielType->nom }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Marque</div>
                <div class="info-value">{{ $attribution->materiel->marque ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Modèle</div>
                <div class="info-value">{{ $attribution->materiel->modele ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Numéro de série</div>
                <div class="info-value"><strong>{{ $attribution->materiel->numero_serie }}</strong></div>
            </div>
            @if($attribution->materiel->specifications_summary)
            <div class="info-row">
                <div class="info-label">Spécifications</div>
                <div class="info-value">{{ $attribution->materiel->specifications_summary }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Informations de l'employé --}}
    <div class="section">
        <div class="section-title">BÉNÉFICIAIRE</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nom complet</div>
                <div class="info-value"><strong>{{ $attribution->employee->full_name }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $attribution->employee->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Service</div>
                <div class="info-value">{{ $attribution->employee->service->nom ?? 'N/A' }}</div>
            </div>
            @if($attribution->employee->fonction)
            <div class="info-row">
                <div class="info-label">Fonction</div>
                <div class="info-value">{{ $attribution->employee->fonction }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Accessoires --}}
    @if($attribution->accessories->count() > 0)
    <div class="section">
        <div class="section-title">ACCESSOIRES FOURNIS</div>
        <ul class="accessories-list">
            @foreach($attribution->accessories as $accessory)
                <li>{{ $accessory->nom }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Observations --}}
    @if($attribution->observations_att)
    <div class="section">
        <div class="section-title">OBSERVATIONS</div>
        <div class="observations">
            {{ $attribution->observations_att }}
        </div>
    </div>
    @endif

    {{-- Signatures --}}
    <div class="signature-section">
        <div class="section-title">SIGNATURES</div>
        <div class="signature-grid">
            <div class="signature-cell">
                <div class="signature-label">L'employé bénéficiaire</div>
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
        Document généré le {{ now()->format('d/m/Y à H:i') }} - Décharge d'attribution {{ $attribution->numero_decharge_att }}
    </div>
</body>
</html>
