<?php

namespace App\Modules\Web\Models;

use Illuminate\Database\Eloquent\Model;

class Complain extends Model
{
    protected $table = 'complains';
    protected $fillable = array(
        "id",
        "pilgrim_data_list_id",
        "country",
        "tracking_no",
        "session_id",
        "pid",
        "agency_license_no",
        "agency_name",
        "is_self",
        "mobile",
        "email",
        "nid",
        "pilgrim_name",
        "complain_reason",
        "comment",
        "complain_attachment",
        "request_data",
        "status",
        "reject_reason",
        "rejected_by",
        "rejected_at",
        "created_at",
        "created_by",
        "updated_at",
        "updated_by",
        "approve_by",
        "approve_at",
        "is_govt",
    );
}
