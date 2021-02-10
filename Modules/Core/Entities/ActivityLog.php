<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;

class ActivityLog extends Model
{

    protected $fillable = [
        'log_name', 'description', 'subject_id',
        'subject_type', 'causer_id',
        'causer_type', 'properties',
        'activity','action'
    ];

    public static $SEARCHABLE = ['log_name', 'description' ,'activity','action'];

    protected $casts = [
        'properties' => 'array'
    ];


    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

}
