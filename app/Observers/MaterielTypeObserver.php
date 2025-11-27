<?php

namespace App\Observers;

use App\Models\MaterielType;
use Illuminate\Support\Facades\Cache;

class MaterielTypeObserver
{
    /**
     * Handle the MaterielType "created" event.
     */
    public function created(MaterielType $materielType): void
    {
        $this->clearCache();
    }

    /**
     * Handle the MaterielType "updated" event.
     */
    public function updated(MaterielType $materielType): void
    {
        $this->clearCache();
    }

    /**
     * Handle the MaterielType "deleted" event.
     */
    public function deleted(MaterielType $materielType): void
    {
        $this->clearCache();
    }

    /**
     * Handle the MaterielType "restored" event.
     */
    public function restored(MaterielType $materielType): void
    {
        $this->clearCache();
    }

    /**
     * Handle the MaterielType "force deleted" event.
     */
    public function forceDeleted(MaterielType $materielType): void
    {
        $this->clearCache();
    }

    /**
     * Clear the materiel types cache.
     */
    protected function clearCache(): void
    {
        Cache::forget('materiel_types.options');
    }
}
