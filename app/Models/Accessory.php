<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Accessory extends Model
{
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'description',
    ];

    /**
     * Get the attributions that have this accessory.
     */
    public function attributions(): BelongsToMany
    {
        return $this->belongsToMany(Attribution::class, 'accessoire_attribution')
            ->withPivot(['statut_att', 'statut_res'])
            ->withTimestamps();
    }
}
