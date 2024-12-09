<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SelfUserHelpText extends Model
{

    protected $table = 'self_user_help_text';
    protected $fillable = array(
        'id',
        'service_name',
        'heder_text',
        'status',
        'user_manual_link',
        'vedio_link',
        'help_text',
        'service_step_image',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    );

}
