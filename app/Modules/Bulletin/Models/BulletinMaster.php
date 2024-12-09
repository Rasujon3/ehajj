<?php

namespace App\Modules\Bulletin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulletinMaster extends Model
{
    use HasFactory;
    protected $table = 'haj_bulletin_master';

    protected $fillable = [
        'bulletin_number',
        'bulletin_subject',
        'haj_type',
        'bulletin_date',
        'bulletin_date_bn',
        'publish_date_time',
        'total_pilgrim',
        'govt_pilgrim',
        'pvt_pilgrim',
        'total_flight',
        'bg_flight',
        'sv_flight',
        'xy_flight',
        'it_helpdesk_service',
        'medical_service',
        'main_massage_text',
        'fixed_text',
        'mail_text_html',
        'status',
        'mail_status',
        'created_by',
        'updated_by',
        'bg_pilgrims',
        'soudia_pilgrims',
        'flynas_pilgrims',
    ];
}
