<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Employee extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_id',
        'nom',
        'prenom',
        'emploi',
        'email',
        'telephone',
        'fonction',
    ];

    /**
     * Get the service that owns the employee.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the attributions for the employee.
     */
    public function attributions(): HasMany
    {
        return $this->hasMany(Attribution::class);
    }

    /**
     * Get the active attributions for the employee.
     */
    public function activeAttributions(): HasMany
    {
        return $this->hasMany(Attribution::class)
            ->whereNull('date_restitution');
    }

    /**
     * Get the full name of the employee.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    /**
     * Get the full name with email.
     */
    public function getFullNameWithEmailAttribute(): string
    {
        return "{$this->full_name} ({$this->email})";
    }

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nom', 'prenom', 'email', 'telephone', 'poste', 'service_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
