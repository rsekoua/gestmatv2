<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Attribution extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'materiel_id',
        'employee_id',
        'date_attribution',
        'date_restitution',
        'numero_decharge_att',
        'numero_decharge_res',
        'observations_att',
        'observations_res',
        'etat_general_res',
        'etat_fonctionnel_res',
        'dommages_res',
        'decision_res',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_attribution' => 'date',
        'date_restitution' => 'date',
        'dommages_res' => 'array',
    ];

    /**
     * Get the materiel that owns the attribution.
     */
    public function materiel(): BelongsTo
    {
        return $this->belongsTo(Materiel::class);
    }

    /**
     * Get the employee that owns the attribution.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the accessories for the attribution.
     */
    public function accessories(): BelongsToMany
    {
        return $this->belongsToMany(Accessory::class, 'accessoire_attribution')
            ->using(AccessoireAttribution::class)
            ->withPivot(['statut_att', 'statut_res'])
            ->withTimestamps();
    }

    /**
     * Get the discharge documents for the attribution.
     */
    public function dischargeDocuments(): HasMany
    {
        return $this->hasMany(DischargeDocument::class);
    }

    /**
     * Check if attribution is active (not returned).
     */
    public function isActive(): bool
    {
        return is_null($this->date_restitution);
    }

    /**
     * Check if attribution is closed (returned).
     */
    public function isClosed(): bool
    {
        return ! is_null($this->date_restitution);
    }

    /**
     * Get the duration of the attribution in days.
     */
    protected function durationInDays(): Attribute
    {
        return Attribute::make(
            get: function (): ?int {
                if ($this->isActive()) {
                    return $this->date_attribution->diffInDays(now());
                }

                return $this->date_attribution->diffInDays($this->date_restitution);
            }
        );
    }

    /**
     * Generate attribution discharge number.
     */
    public static function generateAttributionNumber(): string
    {
        $year = now()->year;
        $lastNumber = static::whereYear('date_attribution', $year)
            ->whereNotNull('numero_decharge_att')
            ->max('numero_decharge_att');

        if ($lastNumber) {
            $number = (int) substr($lastNumber, -4) + 1;
        } else {
            $number = 1;
        }

        return sprintf('ATT-%d-%04d', $year, $number);
    }

    /**
     * Generate restitution discharge number.
     */
    public static function generateRestitutionNumber(): string
    {
        $year = now()->year;
        $lastNumber = static::whereYear('date_restitution', $year)
            ->whereNotNull('numero_decharge_res')
            ->max('numero_decharge_res');

        if ($lastNumber) {
            $number = (int) substr($lastNumber, -4) + 1;
        } else {
            $number = 1;
        }

        return sprintf('RES-%d-%04d', $year, $number);
    }

    /**
     * Scope a query to only include active attributions.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('date_restitution');
    }

    /**
     * Scope a query to only include closed attributions.
     */
    public function scopeClosed($query)
    {
        return $query->whereNotNull('date_restitution');
    }

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'materiel_id',
                'employee_id',
                'date_attribution',
                'date_restitution',
                'observations_att',
                'observations_res',
                'etat_general_res',
                'etat_fonctionnel_res',
                'decision_res',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Générer automatiquement le numéro de décharge d'attribution
        static::creating(function ($attribution) {
            if (empty($attribution->numero_decharge_att)) {
                $attribution->numero_decharge_att = static::generateAttributionNumber();
            }
        });

        // Générer automatiquement le numéro de décharge de restitution
        static::updating(function ($attribution) {
            if ($attribution->isDirty('date_restitution') &&
                ! empty($attribution->date_restitution) &&
                empty($attribution->numero_decharge_res)) {
                $attribution->numero_decharge_res = static::generateRestitutionNumber();
            }
        });
    }
}
