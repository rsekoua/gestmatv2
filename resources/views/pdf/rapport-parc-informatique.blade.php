<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport État du Parc Informatique</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #2563eb;
        }

        .header h1 {
            font-size: 20pt;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .header .subtitle {
            font-size: 11pt;
            color: #666;
            margin-bottom: 3px;
        }

        .header .date {
            font-size: 9pt;
            color: #999;
        }

        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e5e7eb;
        }

        .filters-box {
            background-color: #f3f4f6;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .filters-box .filter-item {
            display: inline-block;
            margin-right: 15px;
            font-size: 9pt;
        }

        .filters-box .filter-label {
            font-weight: bold;
            color: #666;
        }

        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .stats-row {
            display: table-row;
        }

        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }

        .stat-box .stat-value {
            font-size: 18pt;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 3px;
        }

        .stat-box .stat-label {
            font-size: 9pt;
            color: #666;
        }

        .stat-box .stat-subtitle {
            font-size: 8pt;
            color: #999;
            margin-top: 2px;
        }

        .repartition-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .repartition-row {
            display: table-row;
        }

        .repartition-item {
            display: table-cell;
            width: 33.33%;
            padding: 6px;
        }

        .repartition-content {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 8px;
            border-radius: 3px;
        }

        .repartition-content .label {
            font-size: 9pt;
            color: #666;
            margin-bottom: 2px;
        }

        .repartition-content .value {
            font-size: 14pt;
            font-weight: bold;
            color: #2563eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8pt;
        }

        table thead {
            background-color: #2563eb;
            color: white;
        }

        table th {
            padding: 6px 4px;
            text-align: left;
            font-weight: bold;
        }

        table td {
            padding: 5px 4px;
            border-bottom: 1px solid #e5e7eb;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-gray {
            background-color: #e5e7eb;
            color: #4b5563;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #999;
            padding: 10px 0;
            border-top: 1px solid #e5e7eb;
        }

        .page-number:after {
            content: counter(page);
        }

        .summary-boxes {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .summary-row {
            display: table-row;
        }

        .summary-box {
            display: table-cell;
            width: 50%;
            padding: 8px;
        }

        .summary-content {
            background-color: #f3f4f6;
            padding: 10px;
            border-left: 4px solid #2563eb;
        }

        .summary-content .title {
            font-size: 9pt;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-content .value {
            font-size: 16pt;
            font-weight: bold;
            color: #1f2937;
        }

        .summary-content .subtitle {
            font-size: 8pt;
            color: #9ca3af;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    {{-- En-tête --}}
    <div class="header">
        <h1>📊 RAPPORT - ÉTAT DU PARC INFORMATIQUE</h1>
        <div class="subtitle">Inventaire complet et statistiques détaillées</div>
        <div class="date">
            Généré le {{ $date_generation->format('d/m/Y à H:i') }} |
            Date de référence : {{ \Carbon\Carbon::parse($date_reference)->format('d/m/Y') }}
        </div>
    </div>

    {{-- Filtres appliqués --}}
    <div class="filters-box">
        <strong>Filtres appliqués :</strong>
        <span class="filter-item">
            <span class="filter-label">Type:</span> {{ $filtres['type'] }}
        </span>
        <span class="filter-item">
            <span class="filter-label">Service:</span> {{ $filtres['service'] }}
        </span>
        <span class="filter-item">
            <span class="filter-label">Statut:</span> {{ $filtres['statut'] }}
        </span>
    </div>

    {{-- Section 1: Statistiques Globales --}}
    <div class="section">
        <div class="section-title">📈 Statistiques Globales</div>

        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-value">{{ $statistiques['total'] }}</div>
                    <div class="stat-label">Total Matériels</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value" style="color: #10b981;">{{ $statistiques['disponible'] }}</div>
                    <div class="stat-label">Disponibles</div>
                    <div class="stat-subtitle">{{ $statistiques['taux_disponibilite'] }}% du parc</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value" style="color: #3b82f6;">{{ $statistiques['attribue'] }}</div>
                    <div class="stat-label">Attribués</div>
                    <div class="stat-subtitle">{{ $statistiques['taux_utilisation'] }}% en utilisation</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value" style="color: #f59e0b;">{{ $statistiques['en_panne'] + $statistiques['en_maintenance'] }}</div>
                    <div class="stat-label">En Panne / Maintenance</div>
                    <div class="stat-subtitle">{{ $statistiques['en_panne'] }} / {{ $statistiques['en_maintenance'] }}</div>
                </div>
            </div>
        </div>

        <div class="summary-boxes">
            <div class="summary-row">
                <div class="summary-box">
                    <div class="summary-content">
                        <div class="title">Matériels Amortis (> 3 ans)</div>
                        <div class="value">{{ $materiels_amortis }}</div>
                        <div class="subtitle">Ordinateurs uniquement</div>
                    </div>
                </div>
                <div class="summary-box">
                    <div class="summary-content">
                        <div class="title">Attributions Actives</div>
                        <div class="value">{{ $attributions_actives }}</div>
                        <div class="subtitle">Matériels en cours d'utilisation</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 2: Répartition par Type --}}
    @if(!empty($repartition_type))
        <div class="section">
            <div class="section-title">🗂️ Répartition par Type de Matériel</div>

            <div class="repartition-grid">
                @php
                    $chunks = array_chunk($repartition_type, 3, true);
                @endphp
                @foreach($chunks as $chunk)
                    <div class="repartition-row">
                        @foreach($chunk as $type => $count)
                            <div class="repartition-item">
                                <div class="repartition-content">
                                    <div class="label">{{ $type }}</div>
                                    <div class="value">{{ $count }}</div>
                                </div>
                            </div>
                        @endforeach
                        @for($i = count($chunk); $i < 3; $i++)
                            <div class="repartition-item"></div>
                        @endfor
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Section 3: Répartition par Service --}}
    @if(!empty($repartition_service))
        <div class="section">
            <div class="section-title">🏢 Répartition par Service</div>

            <div class="repartition-grid">
                @php
                    $chunks = array_chunk($repartition_service, 3, true);
                @endphp
                @foreach($chunks as $chunk)
                    <div class="repartition-row">
                        @foreach($chunk as $service => $count)
                            <div class="repartition-item">
                                <div class="repartition-content">
                                    <div class="label">{{ $service }}</div>
                                    <div class="value">{{ $count }}</div>
                                </div>
                            </div>
                        @endforeach
                        @for($i = count($chunk); $i < 3; $i++)
                            <div class="repartition-item"></div>
                        @endfor
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Section 4: Liste Détaillée des Matériels --}}
    <div class="section" style="page-break-before: always;">
        <div class="section-title">📋 Liste Détaillée des Matériels</div>

        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Type</th>
                    <th style="width: 15%;">Nom</th>
                    <th style="width: 15%;">Marque/Modèle</th>
                    <th style="width: 12%;">N° Série</th>
                    <th style="width: 10%;">Statut</th>
                    <th style="width: 8%;">État</th>
                    <th style="width: 15%;">Attribué à</th>
                    <th style="width: 13%;">Service</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materiels as $materiel)
                    <tr>
                        <td>{{ $materiel->materielType->nom }}</td>
                        <td><strong>{{ $materiel->nom }}</strong></td>
                        <td>
                            {{ $materiel->marque }}<br>
                            <small style="color: #666;">{{ $materiel->modele }}</small>
                        </td>
                        <td style="font-family: monospace; font-size: 7pt;">{{ $materiel->numero_serie }}</td>
                        <td>
                            @php
                                $badgeClass = match($materiel->statut) {
                                    'disponible' => 'badge-success',
                                    'attribué' => 'badge-info',
                                    'en_panne' => 'badge-danger',
                                    'en_maintenance' => 'badge-warning',
                                    'rebuté' => 'badge-gray',
                                    default => 'badge-gray'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($materiel->statut) }}</span>
                        </td>
                        <td>{{ $materiel->etat_physique ? ucfirst($materiel->etat_physique) : 'N/A' }}</td>
                        <td>{{ $materiel->activeAttribution?->employee?->full_name ?? '-' }}</td>
                        <td>{{ $materiel->activeAttribution?->employee?->service?->nom ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px; color: #999;">
                            Aucun matériel trouvé avec les filtres sélectionnés
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pied de page --}}
    <div class="footer">
        <div>GestMat v2 - Système de Gestion du Parc Informatique</div>
        <div>Page <span class="page-number"></span></div>
    </div>
</body>
</html>
