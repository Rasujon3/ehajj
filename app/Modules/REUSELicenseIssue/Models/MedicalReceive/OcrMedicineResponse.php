<?php
namespace App\Modules\REUSELicenseIssue\Models\MedicalReceive;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class OcrMedicineResponse extends Model
{
    protected $table = 'ocr_api_response';
    protected $guarded = ['id'];
    protected $fillable = array(
        'id',
        'user_id',
        'pharmacy_id',
        'draft_medicine_id',
        'image_url',
        'json_response',
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
