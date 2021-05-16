<?php

namespace Modules\Product\Entities;

use Modules\Core\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Attribute\Entities\Attribute;
use Modules\Core\Entities\Store;
use Modules\UrlRewrite\Traits\HasUrlRewrite;

class ProductAttribute extends Model
{
    use HasFactory, HasUrlRewrite;

    protected $fillable = [ "attribute_id", "channel_id", "product_id", "store_id", "value_type", "value_id" ];
    public $timestamps = false;

    protected $appends = ["value_data", "url"];

    public function __construct(?array $attributes = [])
    {
        parent::__construct($attributes);
        $this->urlRewriteRoute = "admin.catalog.products.show";
        $this->urlRewriteParameter = ["product_id"];
        $this->urlRewriteExtraFields = ["store_id"];
        $this->urlRewriteParameterKey = ["product"];
        $this->urlRewriteRequestPath = (isset($this->store->slug) ? $this->store->slug . "/" : "") . (isset($this->value->value) ? $this->value->value : "");
    }

    public function value(): MorphTo
    {
        return $this->morphTo();
    }

    public function getValueDataAttribute(): mixed
    {
        return $this->value->value;
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    // public function createUrlRewrite(): string
    // {
    //     return (isset($this->store->slug) ? $this->store->slug . "/" : "") . $this->value->value;
    // }
}
