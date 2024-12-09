<?php

namespace App\Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\CommonFunction;

class ShadowFile extends Model {

    protected $table = 'shadow_file';
    protected $fillable = array(
        'id',
        'user_id',
        'process_type_id',
        'ref_id',
        'file_path',
        'shadow_file_perimeter',
        'error_messages',
        'is_generate',
        'is_active',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    );


    public static function boot() {
        parent::boot();
        static::creating(function($post) {
            $post->created_by = CommonFunction::getUserId();
            $post->updated_by = CommonFunction::getUserId();
        });

        static::updating(function($post) {
            $post->updated_by = CommonFunction::getUserId();
        });
    }


    /*     * *****************************************End of Model Class**************************************************** */
}
