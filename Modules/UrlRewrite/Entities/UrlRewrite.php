<?php

namespace Modules\UrlRewrite\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasFactory;
use Modules\UrlRewrite\Traits\HasUrlRewrite;

class UrlRewrite extends Model
{
    use HasFactory, HasUrlRewrite;

    protected $fillable = ["type", "type_attributes", "request_path", "target_path", "redirect_type"];

    public const FORWARD = 0;

    public const PERMANENT = 1;

    public const TEMPORARY = 2;

    protected $casts = [
        'type_attributes' => 'array',
    ];

    public function __construct(?array $attributes = [])
    {   
        parent::__construct($attributes);
    }

    public function isForward(): bool
    {
        return $this->redirect_type === static::FORWARD;
    }

    public function isRedirect(): bool
    {
        return $this->redirect_type !== static::FORWARD;
    }

    public function getRedirectType(): int
    {
        return $this->redirect_type === static::PERMANENT ? 301 : 302;
    }

    public static function getPossibleTypesArray(): array
    {
        $array = [];

        foreach (array_keys(config('url-rewrite.types')) as $type) {
            $array[$type] = $type;
        }

        return $array;
    }
    
}
