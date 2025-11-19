<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceOperation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'materiel_id',
        'maintenance_definition_id',
        'status',
        'scheduled_at',
        'completed_at',
        'notes',
        'performed_by',
    ];

    protected $casts = [
        'scheduled_at' => 'date',
        'completed_at' => 'date',
    ];

    public function materiel(): BelongsTo
    {
        return $this->belongsTo(Materiel::class);
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(MaintenanceDefinition::class, 'maintenance_definition_id');
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'pending')
            ->where('scheduled_at', '<', now());
    }
}
