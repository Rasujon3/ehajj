<?php

namespace App\Modules\GoPassport\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class TempPassportDelivery extends Model
{
    protected $table = 'temp_passport_delivery';
    protected $fillable = [
        'id',
        'passport_no',
        'pilgrim_id',
        'tracking_no',
        'go_serial_no',
        'passport_type',
        'passport_dob',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];


    public static function boot()
    {
        parent::boot();
        // Before update
        static::creating(function($post)
        {
            $post->created_by = CommonFunction::getUserId();
            $post->updated_by = CommonFunction::getUserId();
        });

        static::updating(function($post)
        {
            $post->updated_by = CommonFunction::getUserId();
        });

    }

}