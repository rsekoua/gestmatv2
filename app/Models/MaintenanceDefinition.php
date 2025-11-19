<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceDefinition extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'materiel_type_id',
        'label',
        'description',
        'frequency_days',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'frequency_days' => 'integer',
    ];

    public function materielType(): BelongsTo
    {
        return $this->belongsTo(MaterielType::class);
    }

    public function operations(): HasMany
    {
        return $this->hasMany(MaintenanceOperation::class);
    }
}
