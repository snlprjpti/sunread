<?php

namespace Modules\Page\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageAttribute extends Model
{
    use HasFactory;

    protected $fillable = [ "page_id", "attribute", "value", "position" ];
    protected $casts = [ "value" => "array" ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
