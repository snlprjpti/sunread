<?php

namespace Modules\Page\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Traits\HasFactory;

class PageImage extends Model
{
    use HasFactory;

    protected $fillable = [ "page_id", "path" ];
    protected $appends = [ "path_url" ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function getPathUrlAttribute(): ?string
    {
        if ( !$this->path ) return null;
        return Storage::url($this->path);
    }
}
