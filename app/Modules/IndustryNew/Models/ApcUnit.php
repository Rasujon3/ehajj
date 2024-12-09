<?php


namespace App\Modules\IndustryNew\Models;


use Illuminate\Database\Eloquent\Model;

class ApcUnit extends Model
{
    protected $table = 'apc_units';
    protected $fillable = array(
        'id',
        'name',
        'name_bn',
        'status',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'
    );

    public static function boot()
    {
        parent::boot();
        // Before update
        static::creating(function ($post) {
            $post->created_by = CommonFunction::getUserId();
            $post->updated_by = CommonFunction::getUserId();
        });

        static::updating(function ($post) {
            $post->updated_by = CommonFunction::getUserId();
        });
    }
}
