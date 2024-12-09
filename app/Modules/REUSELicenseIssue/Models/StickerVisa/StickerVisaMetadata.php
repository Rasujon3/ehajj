<?php
namespace App\Modules\REUSELicenseIssue\Models\StickerVisa;

use Illuminate\Database\Eloquent\Model;

class StickerVisaMetadata extends Model
{
    protected $fillable = [
        'ref_id',
        'go_number',
        'go_members',
        'go_date',
        'go_entry_members',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    protected $guarded = ['id'];

}
