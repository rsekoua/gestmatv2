<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Materiel extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'materiel_type_id',
        'marque',
        'modele',
        'numero_serie',
        'processor',
        'ram_size_gb',
        'storage_size_gb',
        'screen_size',
        'purchase_date',
        'acquision',
        'statut',
        'etat_physique',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_date' => 'date',
        'ram_size_gb' => 'integer',
        'storage_size_gb' => 'integer',
        'screen_size' => 'decimal:2',
    ];

    /**
     * Get the type that owns the materiel.
     */
    public function materielType(): BelongsTo
    {
        return $this->belongsTo(MaterielType::class);
    }

    /**
     * Get the attributions for the materiel.
     */
    public function attributions(): HasMany
    {
        return $this->hasMany(Attribution::class);
    }

    /**
     * Get the active attribution for the materiel.
     */
    public function activeAttribution(): HasOne
    {
        return $this->hasOne(Attribution::class)
            ->whereNull('date_restitution')
            ->latestOfMany();
    }

    /**
     * Check if materiel is currently attributed.
     */
    public function isAttributed(): bool
    {
        return $this->statut === 'attribué';
    }

    /**
     * Check if materiel is available for attribution.
     */
    public function isAvailable(): bool
    {
        return $this->statut === 'disponible';
    }

    /**
     * Check if materiel is out of service (rebuté).
     */
    public function isRebutted(): bool
    {
        return $this->statut === 'rebuté';
    }

    /**
     * Check if materiel is depreciated (only for computers).
     * Depreciation applies only to "Ordinateur Portable" and "Ordinateur Bureau" after 3 years.
     */
    protected function isAmorti(): Attribute
    {
        return Attribute::make(
            get: function (): bool {
                // Vérification du type de matériel
                $typesAmortissables = ['Ordinateur Portable', 'Ordinateur Bureau'];

                // Si le type n'est pas dans la liste, pas d'amortissement automatique
                if (!in_array($this->materielType->nom, $typesAmortissables)) {
                    return false;
                }

                // Si c'est un ordinateur, calculer si > 3 ans
                return $this->purchase_date->diffInYears(now()) >= 3;
            }
        );
    }

    /**
     * Get the depreciation status with label.
     */
    protected function amortissementStatus(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                return $this->is_amorti ? 'Amorti' : 'Actif';
            }
        );
    }

    /**
     * Get the name of the materiel (generated from type, marque, modele).
     */
    public function getNomAttribute(): string
    {
        $parts = array_filter([
            $this->materielType?->nom,
            $this->marque,
            $this->modele,
        ]);

        return implode(' - ', $parts) ?: 'Matériel sans nom';
    }

    /**
     * Get the full description of the materiel.
     */
    public function getFullDescriptionAttribute(): string
    {
        return $this->nom;
    }

    /**
     * Get the full description with serial number.
     */
    public function getFullDescriptionWithSerialAttribute(): string
    {
        return "{$this->full_description} (S/N: {$this->numero_serie})";
    }

    /**
     * Get the specifications summary.
     */
    public function getSpecificationsSummaryAttribute(): ?string
    {
        $specs = [];

        if ($this->processor) {
            $specs[] = "CPU: {$this->processor}";
        }

        if ($this->ram_size_gb) {
            $specs[] = "RAM: {$this->ram_size_gb}GB";
        }

        if ($this->storage_size_gb) {
            $specs[] = "Stockage: {$this->storage_size_gb}GB";
        }

        if ($this->screen_size) {
            $specs[] = "Écran: {$this->screen_size}\"";
        }

        return !empty($specs) ? implode(' | ', $specs) : null;
    }

    /**
     * Scope a query to only include available materiels.
     */
    public function scopeAvailable($query)
    {
        return $query->where('statut', 'disponible');
    }

    /**
     * Scope a query to only include attributed materiels.
     */
    public function scopeAttributed($query)
    {
        return $query->where('statut', 'attribué');
    }

    /**
     * Scope a query to only include depreciated computers.
     */
    public function scopeDepreciated($query)
    {
        return $query->whereHas('materielType', function ($q) {
            $q->whereIn('nom', ['Ordinateur Portable', 'Ordinateur Bureau']);
        })
        ->whereDate('purchase_date', '<=', now()->subYears(3));
    }

    /**
     * Scope a query to filter by type.
     */
    public function scopeOfType($query, $typeId)
    {
        return $query->where('materiel_type_id', $typeId);
    }

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'materiel_type_id',
                'marque',
                'modele',
                'numero_serie',
                'processor',
                'ram_size_gb',
                'storage_size_gb',
                'screen_size',
                'statut',
                'etat_physique',
                'purchase_date',
                'acquision',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
