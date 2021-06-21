<?php

namespace Modules\Page\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Traits\HasFactory;

class PageImage extends Model
{
    use HasFactory;

    protected $fillable = [ "page_id","path"];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
