<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Channel extends Model
{
    public static $SEARCHABLE = [ "code", "hostname", "name", "description", "location" ];
    protected $fillable = [ "code", "hostname", "name", "description", "location", "timezone", "logo", "favicon", "theme", "default_store_id", "default_currency", "website_id" ];

    public function default_store(): BelongsTo
    {
        return $this->belongsTo(Store::class, "default_store_id");
    }

    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class);
    }

    private function get_url(?string $path): ?string
    {
        return $path ? Storage::url($path) : null;
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->get_url($this->logo);
    }

    public function getFaviconUrlAttribute(): ?string
    {
        return $this->get_url($this->favicon);
    }
}
