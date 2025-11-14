<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterielType extends Model
{
    use HasFactory, HasUuids;

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
     * Get the materiels for the type.
     */
    public function materiels(): HasMany
    {
        return $this->hasMany(Materiel::class);
    }

    /**
     * Check if this type is a computer.
     */
    public function isComputer(): bool
    {
        return in_array($this->nom, ['Ordinateur Portable', 'Ordinateur Bureau']);
    }

    /**
     * Check if this type supports automatic depreciation.
     */
    public function supportsAutoDepreciation(): bool
    {
        return $this->isComputer();
    }
}
