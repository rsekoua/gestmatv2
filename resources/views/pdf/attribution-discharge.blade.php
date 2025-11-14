<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Décharge d'Attribution - {{ $attribution->numero_decharge_att }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>

        @page {
            margin: 2.5cm 1.5cm 1.5cm;
        }
        body {
            font-family:'Roboto', 'DejaVu Sans', Arial, sans-serif;
            font-size: 9pt;
            color: #1f2937;
            line-height: 1.4;
        }
        .header {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 4px solid #000000;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .header-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .header-logo {
            width: 25%;
            text-align: center;
        }
        .header-logo img {
            height: 60px;
            max-width: 100%;
        }
        .header-title {
            width: 50%;
            text-align: center;
        }
        .header h1 {
            color: #000000;
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
        }
        .header-info-container {
            text-align: center;
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
            /*font-style: italic;*/
            font-size: 10pt;
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
            border: 1px solid #e1e5eb;
            background-color: #ffffff;
            height: 60px;
            padding-top: 40px;
            font-size: 6pt;
            color: #ccc;
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
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    <img src="{{ public_path('storage/logos/MSHPCMU.jpg') }}" alt="Logo MSHPCMU">
                </td>
                <td class="header-title">
                    <h1>DÉCHARGE POUR ATTRIBUTION DE MATÉRIEL</h1>
                </td>
                <td class="header-logo">
                    <img src="{{ public_path('storage/logos/DAP.png') }}" alt="Logo DAP">
                </td>
            </tr>
        </table>
        <div class="header-info-container">
            <div class="header-info"><b>N° {{ $attribution->numero_decharge_att }}</b></div>
            <div class="header-info">Date : {{ $attribution->date_attribution->format('d/m/Y') }}</div>
        </div>
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

    {{-- Informations de l'employé ou du service --}}
    <div class="section">
        <div class="section-title">BÉNÉFICIAIRE</div>
        <table>
            @if($attribution->isForEmployee())
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
            @else
                <tr>
                    <td class="td-label">Service</td>
                    <td class="td-value"><strong>{{ $attribution->service->nom }}</strong></td>
                </tr>
                <tr>
                    <td class="td-label">Code service</td>
                    <td class="td-value">{{ $attribution->service->code ?? 'N/A' }}</td>
                </tr>
                @if($attribution->responsable_service)
                <tr>
                    <td class="td-label">Chef de service</td>
                    <td class="td-value"><strong>{{ $attribution->service->responsable }}</strong></td>
                </tr>
                @endif
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
{{--    @if($attribution->observations_att)--}}
{{--    <div class="section">--}}
{{--        <div class="section-title">OBSERVATIONS</div>--}}
{{--        <div class="observations">--}}
{{--            {{ $attribution->observations_att }}--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    @endif--}}

    {{-- Observations --}}

        <div class="section">
            <div class="observations">
                Par la présente, attestant la remise de matériel informatique, vous
                vous engagez à restituer l'ordinateur à la <b>DAP</b> en bon état à la fin de
                votre contrat.
            </div>
        </div>


    {{-- Signatures --}}
    <div class="signature-section">
{{--        <div class="section-title">SIGNATURES</div>--}}
        <hr>
        <div class="signature-grid">
            <div class="signature-cell">
                @if($attribution->isForEmployee())
                    <div class="signature-label">L'employé bénéficiaire</div>
                    <div class="signature-name">{{ $attribution->employee->full_name }}</div>
                @else
                    <div class="signature-label">Le chef de service</div>
                    <div class="signature-name">{{ $attribution->responsable_service ?? 'N/A' }}</div>
                @endif
                <div class="signature-box">
                    Signature et date
                </div>
            </div>
            <div class="signature-spacer"></div>
            <div class="signature-cell">
                <div class="signature-label">Le responsable</div>
                <div class="signature-name">Service DSI </div>
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
