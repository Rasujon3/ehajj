<?php

namespace App\Modules\CompanyProfile\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class InvestingCountry extends Model {

    protected $table = 'company_inv_country';
    protected $fillable = array(
        'id',
        'org_id',
        'country_id',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
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
