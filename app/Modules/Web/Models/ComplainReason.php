<?php

namespace App\Modules\Web\Models;

use Illuminate\Database\Eloquent\Model;

class ComplainReason extends Model
{
    protected $table = 'complain_reasons';
    protected $fillable = array(
        "id",
        "country",
        "title",
        "status",
    );
}
