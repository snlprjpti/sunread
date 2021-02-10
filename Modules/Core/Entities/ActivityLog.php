<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{

    protected $fillable = [
        'log_name', 'description', 'subject_id',
        'subject_type', 'causer_id',
        'causer_type', 'properties'
    ];

    protected static $SEARCHABLE = ['log_name', 'description'];

    protected $casts = [
        'properties' => 'array'
    ];


    // RELATIONS

    /**
     * Relation with Models with Audit Trait
     * @return MorphTo
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
