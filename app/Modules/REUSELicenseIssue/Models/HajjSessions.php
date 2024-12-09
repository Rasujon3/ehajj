<?php

namespace App\Modules\REUSELicenseIssue\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class HajjSessions extends Model
{

    protected $table = 'hajj_sessions';
    protected $fillable = array(
        'id',
        'caption',
        'state',
        'state',
        'account_number_govt', 'bank_id_govt', 'branch_id_govt',
        'account_number_pvt', 'bank_id_pvt', 'branch_id_pvt',
        'min_pkg_amt_private',
        'prv_pkg_amt_2gvt',
        'adjustable_amount_govt',
        'adjustable_amount_pvt',
        'payement_allowed_govt',
        'payement_allowed_pvt',
        'payment_approved_pvt',
        'payment_approved_govt',
        'pass_expire_date',
        'archived',
    );

    /*     * ******************End of Model Class***************** */
}
