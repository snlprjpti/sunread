<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;


class Core extends Model
{
    protected $fillable = [];

    public static function getRelatedLocales($data):Collection
    {
        if (isset($data['locale']) && is_array($data['locale'])){
            return  Locale::where('code',$data['locale'])->get();
        }
        return Locale::all();
    }
}
