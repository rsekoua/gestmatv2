<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DischargeDocument extends Model
{
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attribution_id',
        'type',
        'numero_decharge',
        'file_path',
        'generated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'generated_at' => 'datetime',
    ];

    /**
     * Get the attribution that owns the discharge document.
     */
    public function attribution(): BelongsTo
    {
        return $this->belongsTo(Attribution::class);
    }

    /**
     * Get the full URL to the document.
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get the file name from the path.
     */
    public function getFileNameAttribute(): string
    {
        return basename($this->file_path);
    }

    /**
     * Check if the file exists.
     */
    public function fileExists(): bool
    {
        return Storage::exists($this->file_path);
    }

    /**
     * Delete the file from storage.
     */
    public function deleteFile(): bool
    {
        if ($this->fileExists()) {
            return Storage::delete($this->file_path);
        }

        return false;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Supprimer le fichier physique lors de la suppression du modèle
        static::deleting(function ($document) {
            $document->deleteFile();
        });
    }
}
