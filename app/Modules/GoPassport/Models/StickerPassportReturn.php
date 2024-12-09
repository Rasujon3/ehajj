<?php

namespace App\Modules\GoPassport\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class StickerPassportReturn extends Model
{
    protected $table = 'sticker_passport_return';
    protected $fillable = [
        'id',
        'purpose',
        'return_date',
        'no_of_pilgrim',
        'comment',
        'tracking_no',
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
