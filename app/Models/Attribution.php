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
        'service_id',
        'responsable_service',
        'date_attribution',
        'date_restitution',
        'numero_decharge_att',
        'numero_decharge_res',
        'decharge_scannee',
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
        // 'dommages_res' => 'array',
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
     * Get the service that owns the attribution.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
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

    protected function formattedDuration(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                // Détermine la date de fin (maintenant ou la date de restitution)
                $endDate = $this->isActive() ? now() : $this->date_restitution;

                // 1. Calcule l'intervalle de date (objet DateInterval)
                $interval = $this->date_attribution->diff($endDate);

                $parts = [];

                // 2. Construit le tableau des parties de la chaîne
                if ($interval->y > 0) {
                    $parts[] = $interval->y.' an'.($interval->y > 1 ? 's' : '');
                }
                if ($interval->m > 0) {
                    $parts[] = $interval->m.' mois'; // 'mois' est invariable
                }
                if ($interval->d > 0) {
                    $parts[] = $interval->d.' jour'.($interval->d > 1 ? 's' : '');
                }

                // 3. Gère le cas où la durée est de 0 jour
                if (empty($parts)) {
                    return '0 jours';
                }

                // 4. Combine les parties
                return implode(' ', $parts);
            }
        );
    }

    /**
     * Generate attribution discharge number.
     */
    public static function generateAttributionNumber(): string
    {
        // 1. Récupère les infos de date actuelles
        $now = now();
        $yearYYYY = $now->year;         // Année sur 4 chiffres (ex: 2025)
        $yearYY = $now->format('y');   // Année sur 2 chiffres (ex: 25)
        $monthMM = $now->format('m');  // Mois sur 2 chiffres (ex: 11)

        // 2. Crée le préfixe du numéro
        $prefix = "ATT-{$yearYY}{$monthMM}-";

        // 3. Trouve le dernier numéro utilisé pour ce mois/année
        $lastNumber = static::whereYear('date_attribution', $yearYYYY)
            ->whereMonth('date_attribution', $monthMM)
            ->whereNotNull('numero_decharge_att')
            ->where('numero_decharge_att', 'like', "{$prefix}%")
            ->orderByRaw('CAST(SUBSTR(numero_decharge_att, -3) AS INTEGER) DESC')
            ->value('numero_decharge_att');

        // 4. Extrait le numéro séquentiel et incrémente
        if ($lastNumber) {
            $lastSequence = (int) substr($lastNumber, -3);
            $number = $lastSequence + 1;
        } else {
            $number = 1;
        }

        // 5. En cas de collision (très rare), trouve le prochain numéro disponible
        $maxAttempts = 100;
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $generatedNumber = sprintf('%s%03d', $prefix, $number);

            // Vérifie si ce numéro existe déjà
            $exists = static::where('numero_decharge_att', $generatedNumber)->exists();

            if (! $exists) {
                return $generatedNumber;
            }

            $number++;
        }

        // 6. Si après 100 tentatives on n'a pas trouvé, utilise un timestamp
        return sprintf('%s%s', $prefix, substr(str_replace('.', '', microtime(true)), -3));
    }

    /**
     * Generate restitution discharge number.
     */
    public static function generateRestitutionNumber(): string
    {
        // 1. Récupère les infos de date actuelles
        $now = now();
        $yearYYYY = $now->year;
        $yearYY = $now->format('y');
        $monthMM = $now->format('m');

        // 2. Crée le préfixe du numéro
        $prefix = "RES-{$yearYY}{$monthMM}-";

        // 3. Trouve le dernier numéro utilisé pour ce mois/année
        $lastNumber = static::whereYear('date_restitution', $yearYYYY)
            ->whereMonth('date_restitution', $monthMM)
            ->whereNotNull('numero_decharge_res')
            ->where('numero_decharge_res', 'like', "{$prefix}%")
            ->orderByRaw('CAST(SUBSTR(numero_decharge_res, -3) AS INTEGER) DESC')
            ->value('numero_decharge_res');

        // 4. Extrait le numéro séquentiel et incrémente
        if ($lastNumber) {
            $lastSequence = (int) substr($lastNumber, -3);
            $number = $lastSequence + 1;
        } else {
            $number = 1;
        }

        // 5. En cas de collision (très rare), trouve le prochain numéro disponible
        $maxAttempts = 100;
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $generatedNumber = sprintf('%s%03d', $prefix, $number);

            // Vérifie si ce numéro existe déjà
            $exists = static::where('numero_decharge_res', $generatedNumber)->exists();

            if (! $exists) {
                return $generatedNumber;
            }

            $number++;
        }

        // 6. Si après 100 tentatives on n'a pas trouvé, utilise un timestamp
        return sprintf('%s%s', $prefix, substr(str_replace('.', '', microtime(true)), -3));
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
     * Get the recipient name (employee or service).
     */
    public function getRecipientNameAttribute(): string
    {
        if ($this->employee_id) {
            return $this->employee->full_name;
        }

        if ($this->service_id) {
            return $this->service->nom;
        }

        return 'Non défini';
    }

    /**
     * Check if attribution is for an employee.
     */
    public function isForEmployee(): bool
    {
        return ! is_null($this->employee_id);
    }

    /**
     * Check if attribution is for a service.
     */
    public function isForService(): bool
    {
        return ! is_null($this->service_id);
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
                'service_id',
                'responsable_service',
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
