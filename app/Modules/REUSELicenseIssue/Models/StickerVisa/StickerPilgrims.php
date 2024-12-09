<?php
/**
 * Author: Md. Mehedi Hasan Ahad
 * Date: 29 Nov, 2022
 */
namespace App\Modules\REUSELicenseIssue\Models\StickerVisa;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class StickerPilgrims extends Model
{
    protected $table = 'sticker_pilgrims';
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'ref_id',
        'dob',
        'gender',
        'identity_type',
        'identity_no',
        'passport_type',
        'passport_no',
        'passport_dob',
        'mobile_no',
        'go_serial_no',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'amount',
        'taka_received_date',
        'referance_no',
        'pid',
        'pilgrim_tracking_no',
        'passport_response',
        'is_archived',
        'archived_reason',
        'is_delivery',
        'sticker_passport_return_id'
    ];

}
