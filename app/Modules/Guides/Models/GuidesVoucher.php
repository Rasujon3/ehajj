<?php

namespace App\Modules\Guides\Models;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\CommonFunction;

class GuidesVoucher extends Model {

    protected $table = 'guides_voucher';
    protected $fillable = [
        'id',
        'guide_id',
        'reg_voucher_id',
        'session_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public static function boot() {
        parent::boot();
        // Before update
        static::creating(function($post) {
            $post->created_by = CommonFunction::getUserId();
            $post->updated_by = CommonFunction::getUserId();
        });

        static::updating(function($post) {
            $post->updated_by = CommonFunction::getUserId();
        });
    }

    /*     * *****************************End of Model Function********************************* */
}
