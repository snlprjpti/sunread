<?php

namespace Modules\EmailTemplate\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasFactory;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [ "template_name", "template_subject", "template_content", "template_style" ];
}
