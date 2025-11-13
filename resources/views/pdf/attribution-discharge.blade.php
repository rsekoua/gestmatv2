<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Décharge d'Attribution - {{ $attribution->numero_decharge_att }}</title>
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
            border-bottom: 4px solid #000000;
            /*border-bottom: 4px solid #4f46e5;*/
        }
        .header h1 {
            color: #000000;
            /*color: #433_8ca;*/
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
            background-color: #e0e7ff;
         /* background-color: #e0e7ff;*/
            color: #000000;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 5px;
           /* border-left: 4px solid #4f46e5;*/
            border-left: 4px solid #000000;
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
        ul {
            margin: 0;
            padding-left: 20px;
            font-size: 8pt;
        }
        ul li {
            padding: 2px 0;
        }
        .observations {
            background-color: #f9fafb;
            padding: 8px;
            border-left: 4px solid #9ca3af;
            font-style: italic;
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
            height: 70px;
            padding-top: 50px;
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
        <h1>DÉCHARGE POUR ATTRIBUTION DE MATÉRIEL</h1>
        <div class="header-info"><b>N° {{ $attribution->numero_decharge_att }}</b></div>
        <div class="header-info">Date : {{ $attribution->date_attribution->format('d/m/Y') }}</div>
    </div>

    {{-- Informations du matériel --}}
    <div class="section">
        <div class="section-title">INFORMATIONS DU MATÉRIEL</div>
        <table>
            <tr>
                <td class="td-label">Type de matériel</td>
                <td class="td-value">{{ $attribution->materiel->materielType->nom }}</td>
            </tr>
            <tr>
                <td class="td-label">Marque</td>
                <td class="td-value">{{ $attribution->materiel->marque ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="td-label">Modèle</td>
                <td class="td-value">{{ $attribution->materiel->modele ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="td-label">Numéro de série</td>
                <td class="td-value"><strong>{{ $attribution->materiel->numero_serie }}</strong></td>
            </tr>
            @if($attribution->materiel->specifications_summary)
            <tr>
                <td class="td-label">Spécifications</td>
                <td class="td-value">{{ $attribution->materiel->specifications_summary }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Informations de l'employé --}}
    <div class="section">
        <div class="section-title">BÉNÉFICIAIRE</div>
        <table>
            <tr>
                <td class="td-label">Nom complet</td>
                <td class="td-value"><strong>{{ $attribution->employee->full_name }}</strong></td>
            </tr>
            <tr>
                <td class="td-label">Email</td>
                <td class="td-value">{{ $attribution->employee->email }}</td>
            </tr>
            <tr>
                <td class="td-label">Service</td>
                <td class="td-value">{{ $attribution->employee->service->nom ?? 'N/A' }}</td>
            </tr>
            @if($attribution->employee->fonction)
            <tr>
                <td class="td-label">Fonction</td>
                <td class="td-value">{{ $attribution->employee->fonction }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Accessoires --}}
    @if($attribution->accessories->count() > 0)
    <div class="section">
        <div class="section-title">ACCESSOIRES FOURNIS</div>
        <ul>
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
                <div class="signature-name">{{ $attribution->employee->full_name }}</div>
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
        Document généré le {{ now()->format('d/m/Y à H:i') }} - Décharge d'attribution {{ $attribution->numero_decharge_att }}
    </div>
</body>
</html>
