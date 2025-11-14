<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Décharge de Restitution - {{ $attribution->numero_decharge_res }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            color: #1f2937;
            line-height: 1.6;
            background-color: #f3f4f6;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: white;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 4px solid #dc2626;
        }
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 15px;
        }
        .header-logo {
            flex-shrink: 0;
        }
        .header-logo img {
            height: 70px;
            width: auto;
            object-fit: contain;
        }
        .header-title {
            flex: 1;
            text-align: center;
        }
        .header h1 {
            color: #991b1b;
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header-info-container {
            text-align: center;
        }
        .header-info {
            font-size: 14px;
            color: #4b5563;
            margin: 5px 0;
        }
        .section {
            margin-bottom: 10px;
        }
        .section-title {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            border-left: 4px solid #dc2626;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        td {
            padding: 10px 12px;
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
            padding-left: 25px;
            font-size: 13px;
        }
        ul li {
            padding: 5px 0;
        }
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-green { background-color: #d1fae5; color: #065f46; }
        .badge-blue { background-color: #dbeafe; color: #1e40af; }
        .badge-yellow { background-color: #fef3c7; color: #92400e; }
        .badge-red { background-color: #fee2e2; color: #991b1b; }
        .observations {
            background-color: #f9fafb;
            padding: 15px;
            border-left: 4px solid #9ca3af;
            font-style: italic;
            font-size: 13px;
            min-height: 50px;
        }
        .damages {
            background-color: #fef2f2;
            padding: 15px;
            border-left: 4px solid #dc2626;
            font-size: 13px;
            min-height: 50px;
        }
        .signature-section {
            margin-top: 10px;
        }
        .signature-grid {
            display: flex;
            justify-content: space-between;
            gap: 40px;
            margin-top: 20px;
        }
        .signature-cell {
            flex: 1;
            text-align: center;
        }
        .signature-label {
            font-weight: bold;
            color: #374151;
            margin-bottom: 10px;
            font-size: 13px;
        }
        .signature-name {
            margin-bottom: 10px;
            font-size: 13px;
        }
        .signature-box {
            border: 2px solid #d1d5db;
            background-color: #f9fafb;
            height: 100px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding-bottom: 10px;
            font-size: 12px;
            color: #6b7280;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            padding-top: 20px;
            margin-top: 40px;
            border-top: 1px solid #e5e7eb;
        }
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #dc2626;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transition: background-color 0.2s;
        }
        .print-button:hover {
            background-color: #b91c1c;
        }
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            .container {
                box-shadow: none;
                padding: 20px;
            }
            .print-button {
                display: none;
            }
            .header-logo img {
                height: 70px;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">🖨️ Imprimer</button>

    <div class="container">
        <div class="header">
            <div class="header-content">
                <div class="header-logo">
                    <img src="{{ asset('storage/logos/MSHPCMU.jpg') }}" alt="Logo MSHPCMU">
                </div>
                <div class="header-title">
                    <h1>DÉCHARGE DE RESTITUTION DE MATÉRIEL</h1>
                </div>
                <div class="header-logo">
                    <img src="{{ asset('storage/logos/DAP.png') }}" alt="Logo DAP">
                </div>
            </div>
            <div class="header-info-container">
                <div class="header-info">N° {{ $attribution->numero_decharge_res }}</div>
                <div class="header-info">Date : {{ $attribution->date_restitution->format('d/m/Y') }}</div>
            </div>
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
                            {{ $attribution->formatted_duration }}
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



        {{-- Accessoires restitués --}}
        @if($attribution->accessories->count() > 0)
        <div class="section">
            <div class="section-title">ACCESSOIRES RESTITUÉS</div>
            {{-- Pluck récupère tous les 'nom' et 'join' les assemble --}}
             {{ $attribution->accessories->pluck('nom')->join(' | ') }}
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
    </div>
</body>
</html>
