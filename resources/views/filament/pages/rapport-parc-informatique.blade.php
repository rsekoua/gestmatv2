<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Formulaire de filtres --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-funnel" class="w-5 h-5" />
                    <span>Paramètres du Rapport</span>
                </div>
            </x-slot>

            <form wire:submit.prevent="$refresh" class="space-y-6">
                {{ $this->form }}

                <x-filament::button type="submit" color="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Générer le rapport</span>
                    <span wire:loading>Génération en cours...</span>
                </x-filament::button>
            </form>
        </x-filament::section>

        {{-- Statistiques globales --}}
        @php
            $stats = $this->getStatistiquesGlobales();
        @endphp

        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-chart-bar" class="w-5 h-5" />
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
                        <x-filament::icon icon="heroicon-o-computer-desktop" class="h-12 w-12 text-primary-500" />
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
                        <x-filament::icon icon="heroicon-o-check-circle" class="h-12 w-12 text-success-500" />
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
                        <x-filament::icon icon="heroicon-o-user-circle" class="h-12 w-12 text-info-500" />
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
                        <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-12 w-12 text-warning-500" />
                    </div>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                {{-- Matériels amortis --}}
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Matériels Amortis (>3
                                ans)</p>
                            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->getMaterielsAmortis() }}</p>
                            <p class="mt-1 text-xs text-gray-500">Ordinateurs uniquement</p>
                        </div>
                        <x-filament::icon icon="heroicon-o-calendar-days" class="h-10 w-10 text-gray-400" />
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
                        <x-filament::icon icon="heroicon-o-arrow-right-circle" class="h-10 w-10 text-gray-400" />
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
                        <x-filament::icon icon="heroicon-o-squares-2x2" class="w-5 h-5" />
                        <span>Répartition par Type de Matériel</span>
                    </div>
                </x-slot>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($repartitionType as $type => $count)
                        <div
                            class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/50">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $type }}</span>
                            <span
                                class="rounded-full bg-primary-100 px-3 py-1 text-sm font-bold text-primary-700 dark:bg-primary-500/20 dark:text-primary-400">
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
                        <x-filament::icon icon="heroicon-o-building-office-2" class="w-5 h-5" />
                        <span>Répartition par Service</span>
                    </div>
                </x-slot>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($repartitionService as $service => $count)
                        <div
                            class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/50">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $service }}</span>
                            <span
                                class="rounded-full bg-info-100 px-3 py-1 text-sm font-bold text-info-700 dark:bg-info-500/20 dark:text-info-400">
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
                    <x-filament::icon icon="heroicon-o-list-bullet" class="w-5 h-5" />
                    <span>Liste Détaillée des Matériels</span>
                </div>
            </x-slot>

            <div class="fi-ta-ctn divide-y divide-gray-200 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">
                <div class="overflow-x-auto">
                    <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                        <thead class="divide-y divide-gray-200 dark:divide-white/5">
                            <tr class="bg-gray-50 dark:bg-white/5">
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                    <span class="text-sm font-semibold text-gray-950 dark:text-white">Type</span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5">
                                    <span class="text-sm font-semibold text-gray-950 dark:text-white">Nom</span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5">
                                    <span class="text-sm font-semibold text-gray-950 dark:text-white">Marque/Modèle</span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5">
                                    <span class="text-sm font-semibold text-gray-950 dark:text-white">N° Série</span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5">
                                    <span class="text-sm font-semibold text-gray-950 dark:text-white">Statut</span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5">
                                    <span class="text-sm font-semibold text-gray-950 dark:text-white">État Physique</span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5">
                                    <span class="text-sm font-semibold text-gray-950 dark:text-white">Attribué à</span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:last-of-type:pe-6">
                                    <span class="text-sm font-semibold text-gray-950 dark:text-white">Service</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                            @forelse($this->getMateriels() as $materiel)
                                <tr class="fi-ta-row hover:bg-gray-50 dark:hover:bg-white/5">
                                    <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                        <div class="fi-ta-col-wrp px-3 py-4">
                                            <div class="flex items-center gap-x-3">
                                                <div class="flex-1">
                                                    <x-filament::badge color="gray">
                                                        {{ $materiel->materielType->nom }}
                                                    </x-filament::badge>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fi-ta-cell p-0">
                                        <div class="fi-ta-col-wrp px-3 py-4">
                                            <div class="fi-ta-text-item">
                                                <div class="fi-ta-text-item-label text-sm font-medium text-gray-950 dark:text-white">
                                                    {{ $materiel->nom }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fi-ta-cell p-0">
                                        <div class="fi-ta-col-wrp px-3 py-4">
                                            <div class="fi-ta-text-item">
                                                <div class="fi-ta-text-item-label text-sm text-gray-950 dark:text-white">
                                                    {{ $materiel->marque }}
                                                </div>
                                                <div class="fi-ta-text-item-description text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $materiel->modele }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fi-ta-cell p-0">
                                        <div class="fi-ta-col-wrp px-3 py-4">
                                            <div class="fi-ta-text-item">
                                                <code class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $materiel->numero_serie }}
                                                </code>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fi-ta-cell p-0">
                                        <div class="fi-ta-col-wrp px-3 py-4">
                                            @php
                                                $statutColor = match($materiel->statut) {
                                                    'disponible' => 'success',
                                                    'attribué' => 'info',
                                                    'en_panne' => 'danger',
                                                    'en_maintenance' => 'warning',
                                                    'rebuté' => 'gray',
                                                    default => 'gray'
                                                };
                                            @endphp
                                            <x-filament::badge :color="$statutColor">
                                                {{ ucfirst($materiel->statut) }}
                                            </x-filament::badge>
                                        </div>
                                    </td>
                                    <td class="fi-ta-cell p-0">
                                        <div class="fi-ta-col-wrp px-3 py-4">
                                            <div class="fi-ta-text-item-label text-sm text-gray-700 dark:text-gray-300">
                                                {{ $materiel->etat_physique ? ucfirst($materiel->etat_physique) : 'N/A' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fi-ta-cell p-0">
                                        <div class="fi-ta-col-wrp px-3 py-4">
                                            <div class="fi-ta-text-item-label text-sm text-gray-700 dark:text-gray-300">
                                                {{ $materiel->activeAttribution?->employee?->full_name ?? '-' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fi-ta-cell p-0">
                                        <div class="fi-ta-col-wrp px-3 py-4">
                                            <div class="fi-ta-text-item-label text-sm text-gray-700 dark:text-gray-300">
                                                {{ $materiel->activeAttribution?->employee?->service?->nom ?? '-' }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="fi-ta-empty-state-ctn p-6">
                                        <div class="fi-ta-empty-state mx-auto grid max-w-lg justify-items-center text-center">
                                            <div class="fi-ta-empty-state-icon-ctn mb-4 rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                                                <x-filament::icon icon="heroicon-o-x-mark" class="h-6 w-6 text-gray-500 dark:text-gray-400" />
                                            </div>
                                            <h4 class="fi-ta-empty-state-heading text-base font-semibold text-gray-950 dark:text-white">
                                                Aucun matériel trouvé
                                            </h4>
                                            <p class="fi-ta-empty-state-description text-sm text-gray-500 dark:text-gray-400">
                                                Aucun matériel ne correspond aux filtres sélectionnés
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
