<?php

namespace App\Modules\REUSELicenseIssue\Models\MedicalReceive;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class DraftMedicine extends Model
{
    protected $table = 'ocr_draft_medicine';
    protected $fillable = array(
        'id',
        'pid',
        'pharmacy_id',
        'is_draft',
        'issue_checker',
        'session_id',
        'image_url',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
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
