<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center gap-3 mb-4">
{{--                <x-heroicon-o-information-circle class="w-6 h-6 text-blue-500" />--}}
                <h3 class="text-lg font-semibold">Comment ça fonctionne ?</h3>
            </div>
            <div class="prose dark:prose-invert max-w-none">
                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                    <li>Les imports sont traités en <strong>arrière-plan</strong> via la queue</li>
                    <li>La progression est mise à jour <strong>automatiquement toutes les 5 secondes</strong></li>
                    <li>En cas d'erreur, vous pouvez télécharger le rapport d'erreurs depuis les notifications</li>
                    <li>Les imports terminés restent visibles dans cet historique</li>
                </ul>
            </div>
        </div>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
