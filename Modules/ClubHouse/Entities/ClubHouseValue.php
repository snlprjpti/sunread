<?php

namespace Modules\ClubHouse\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClubHouseValue extends Model
{
    use HasFactory;

    protected $fillable = ['club_house_id', 'scope', 'scope_id', 'attribute', 'value'];

    /**
     * Many to One Relation Between ClubHouseValue and ClubHouse
     * @return BelongsTo
     */
    public function clubHouse(): BelongsTo
    {
        return $this->belongsTo(ClubHouse::class);
    }

}
