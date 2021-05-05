<?php

namespace Modules\UrlRewrite\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UrlRewrite extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\UrlRewrite\Database\factories\UrlRewriteFactory::new();
    }
}
