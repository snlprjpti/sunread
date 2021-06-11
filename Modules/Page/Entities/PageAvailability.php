<?php

namespace Modules\Page\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Core\Traits\HasFactory;

class PageAvailability extends Model
{
    use HasFactory;
    protected $table = "page_availability";

    protected $fillable = [ "page_id", "model_type", "model_id", "status" ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, "page_id");
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
