<?php
namespace App\Modules\REUSELicenseIssue\Models\MedicalReceive;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class PilgrimMedIssue extends Model
{
    protected $table = 'pilgrim_medicine_issue';
    protected $guarded = ['id'];

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
