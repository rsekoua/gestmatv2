<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                @svg('heroicon-o-bell', 'w-5 h-5')
                <span>Alertes et Notifications</span>
            </div>
        </x-slot>

        <div class="space-y-3">
            @forelse($this->getAlerts() as $alert)
                @php
                    $colorClasses = match($alert['type']) {
                        'success' => [
                            'bg' => 'bg-success-50 dark:bg-success-400/10',
                            'border' => 'border-success-200 dark:border-success-400/20',
                            'icon' => 'text-success-600 dark:text-success-400',
                            'title' => 'text-success-800 dark:text-success-300',
                            'text' => 'text-success-700 dark:text-success-400',
                            'link' => 'text-success-700 hover:text-success-800 dark:text-success-400 dark:hover:text-success-300',
                        ],
                        'warning' => [
                            'bg' => 'bg-warning-50 dark:bg-warning-400/10',
                            'border' => 'border-warning-200 dark:border-warning-400/20',
                            'icon' => 'text-warning-600 dark:text-warning-400',
                            'title' => 'text-warning-800 dark:text-warning-300',
                            'text' => 'text-warning-700 dark:text-warning-400',
                            'link' => 'text-warning-700 hover:text-warning-800 dark:text-warning-400 dark:hover:text-warning-300',
                        ],
                        'danger' => [
                            'bg' => 'bg-danger-50 dark:bg-danger-400/10',
                            'border' => 'border-danger-200 dark:border-danger-400/20',
                            'icon' => 'text-danger-600 dark:text-danger-400',
                            'title' => 'text-danger-800 dark:text-danger-300',
                            'text' => 'text-danger-700 dark:text-danger-400',
                            'link' => 'text-danger-700 hover:text-danger-800 dark:text-danger-400 dark:hover:text-danger-300',
                        ],
                        'info' => [
                            'bg' => 'bg-info-50 dark:bg-info-400/10',
                            'border' => 'border-info-200 dark:border-info-400/20',
                            'icon' => 'text-info-600 dark:text-info-400',
                            'title' => 'text-info-800 dark:text-info-300',
                            'text' => 'text-info-700 dark:text-info-400',
                            'link' => 'text-info-700 hover:text-info-800 dark:text-info-400 dark:hover:text-info-300',
                        ],
                        default => [
                            'bg' => 'bg-gray-50 dark:bg-gray-400/10',
                            'border' => 'border-gray-200 dark:border-gray-400/20',
                            'icon' => 'text-gray-600 dark:text-gray-400',
                            'title' => 'text-gray-800 dark:text-gray-300',
                            'text' => 'text-gray-700 dark:text-gray-400',
                            'link' => 'text-gray-700 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300',
                        ],
                    };
                @endphp

                <div class="flex items-start gap-3 p-4 rounded-lg border {{ $colorClasses['bg'] }} {{ $colorClasses['border'] }}">
                    <div class="flex-shrink-0 mt-0.5">
                        @if($alert['type'] === 'success')
                            @svg('heroicon-o-check-circle', 'w-6 h-6 ' . $colorClasses['icon'])
                        @elseif($alert['type'] === 'warning')
                            @svg('heroicon-o-exclamation-triangle', 'w-6 h-6 ' . $colorClasses['icon'])
                        @elseif($alert['type'] === 'danger')
                            @svg('heroicon-o-exclamation-circle', 'w-6 h-6 ' . $colorClasses['icon'])
                        @elseif($alert['type'] === 'info')
                            @svg('heroicon-o-information-circle', 'w-6 h-6 ' . $colorClasses['icon'])
                        @else
                            @svg('heroicon-o-bell', 'w-6 h-6 ' . $colorClasses['icon'])
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold {{ $colorClasses['title'] }}">
                            {{ $alert['title'] }}
                        </h3>
                        <p class="mt-1 text-sm {{ $colorClasses['text'] }}">
                            {{ $alert['message'] }}
                        </p>
                    </div>

                    @if(!empty($alert['action']) && !empty($alert['url']))
                        <div class="flex-shrink-0">
                            <a href="{{ $alert['url'] }}"
                               class="inline-flex items-center gap-1 text-sm font-medium transition-colors {{ $colorClasses['link'] }}">
                                {{ $alert['action'] }}
                                @svg('heroicon-o-arrow-right', 'w-4 h-4')
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="flex items-center justify-center p-8 text-gray-500 dark:text-gray-400">
                    <div class="text-center">
                        @svg('heroicon-o-check-circle', 'w-12 h-12 mx-auto mb-3 text-success-500')
                        <p class="text-sm font-medium">Aucune alerte</p>
                        <p class="text-xs mt-1">Tout fonctionne correctement</p>
                    </div>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
