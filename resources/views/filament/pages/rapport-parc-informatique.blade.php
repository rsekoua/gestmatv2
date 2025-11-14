<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Formulaire de filtres --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    @svg('heroicon-o-funnel', 'w-5 h-5')
                    <span>Paramètres du Rapport</span>
                </div>
            </x-slot>

            <x-filament-panels::form wire:submit="$refresh">
                {{ $this->form }}

                <div class="mt-4">
                    <x-filament::button type="submit" color="primary">
                        Générer le rapport
                    </x-filament::button>
                </div>
            </x-filament-panels::form>
        </x-filament::section>

        {{-- Statistiques globales --}}
        @php
            $stats = $this->getStatistiquesGlobales();
        @endphp

        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    @svg('heroicon-o-chart-bar', 'w-5 h-5')
                    <span>Statistiques Globales</span>
                </div>
            </x-slot>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Total matériels --}}
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Matériels</p>
                            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total'] }}</p>
                        </div>
                        @svg('heroicon-o-computer-desktop', 'h-12 w-12 text-primary-500')
                    </div>
                </div>

                {{-- Disponibles --}}
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Disponibles</p>
                            <p class="mt-1 text-3xl font-bold text-success-600 dark:text-success-400">{{ $stats['disponible'] }}</p>
                            <p class="mt-1 text-xs text-gray-500">{{ $stats['taux_disponibilite'] }}% du parc</p>
                        </div>
                        @svg('heroicon-o-check-circle', 'h-12 w-12 text-success-500')
                    </div>
                </div>

                {{-- Attribués --}}
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Attribués</p>
                            <p class="mt-1 text-3xl font-bold text-info-600 dark:text-info-400">{{ $stats['attribue'] }}</p>
                            <p class="mt-1 text-xs text-gray-500">{{ $stats['taux_utilisation'] }}% en utilisation</p>
                        </div>
                        @svg('heroicon-o-user-circle', 'h-12 w-12 text-info-500')
                    </div>
                </div>

                {{-- En panne / Maintenance --}}
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">En Panne / Maintenance</p>
                            <p class="mt-1 text-3xl font-bold text-warning-600 dark:text-warning-400">
                                {{ $stats['en_panne'] + $stats['en_maintenance'] }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ $stats['en_panne'] }} en panne · {{ $stats['en_maintenance'] }} en maintenance
                            </p>
                        </div>
                        @svg('heroicon-o-exclamation-triangle', 'h-12 w-12 text-warning-500')
                    </div>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                {{-- Matériels amortis --}}
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Matériels Amortis (>3 ans)</p>
                            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->getMaterielsAmortis() }}</p>
                            <p class="mt-1 text-xs text-gray-500">Ordinateurs uniquement</p>
                        </div>
                        @svg('heroicon-o-calendar-days', 'h-10 w-10 text-gray-400')
                    </div>
                </div>

                {{-- Attributions actives --}}
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Attributions Actives</p>
                            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->getAttributionsActives() }}</p>
                            <p class="mt-1 text-xs text-gray-500">Matériels actuellement en utilisation</p>
                        </div>
                        @svg('heroicon-o-arrow-right-circle', 'h-10 w-10 text-gray-400')
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- Répartition par type --}}
        @php
            $repartitionType = $this->getRepartitionParType();
        @endphp

        @if(!empty($repartitionType))
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        @svg('heroicon-o-squares-2x2', 'w-5 h-5')
                        <span>Répartition par Type de Matériel</span>
                    </div>
                </x-slot>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($repartitionType as $type => $count)
                        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/50">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $type }}</span>
                            <span class="rounded-full bg-primary-100 px-3 py-1 text-sm font-bold text-primary-700 dark:bg-primary-500/20 dark:text-primary-400">
                                {{ $count }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endif

        {{-- Répartition par service --}}
        @php
            $repartitionService = $this->getRepartitionParService();
        @endphp

        @if(!empty($repartitionService))
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        @svg('heroicon-o-building-office-2', 'w-5 h-5')
                        <span>Répartition par Service</span>
                    </div>
                </x-slot>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($repartitionService as $service => $count)
                        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/50">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $service }}</span>
                            <span class="rounded-full bg-info-100 px-3 py-1 text-sm font-bold text-info-700 dark:bg-info-500/20 dark:text-info-400">
                                {{ $count }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endif

        {{-- Liste détaillée des matériels --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    @svg('heroicon-o-list-bullet', 'w-5 h-5')
                    <span>Liste Détaillée des Matériels</span>
                </div>
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">Type</th>
                            <th class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">Nom</th>
                            <th class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">Marque/Modèle</th>
                            <th class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">N° Série</th>
                            <th class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">Statut</th>
                            <th class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">État Physique</th>
                            <th class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">Attribué à</th>
                            <th class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">Service</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($this->getMateriels() as $materiel)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                        {{ $materiel->materielType->nom }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $materiel->nom }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ $materiel->marque }}<br>
                                    <span class="text-xs">{{ $materiel->modele }}</span>
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400">{{ $materiel->numero_serie }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $statutColor = match($materiel->statut) {
                                            'disponible' => 'bg-success-100 text-success-700 dark:bg-success-500/20 dark:text-success-400',
                                            'attribué' => 'bg-info-100 text-info-700 dark:bg-info-500/20 dark:text-info-400',
                                            'en_panne' => 'bg-danger-100 text-danger-700 dark:bg-danger-500/20 dark:text-danger-400',
                                            'en_maintenance' => 'bg-warning-100 text-warning-700 dark:bg-warning-500/20 dark:text-warning-400',
                                            'rebuté' => 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-400',
                                            default => 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-400'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $statutColor }}">
                                        {{ ucfirst($materiel->statut) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ $materiel->etat_physique ? ucfirst($materiel->etat_physique) : 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ $materiel->activeAttribution?->employee?->full_name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ $materiel->activeAttribution?->employee?->service?->nom ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Aucun matériel trouvé avec les filtres sélectionnés
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
