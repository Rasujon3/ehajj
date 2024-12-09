<?php

namespace App\Modules\ProcessPath\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class   ProcessType extends Model
{

    protected $table = 'process_type';
    public $timestamps = false;
    protected $fillable = array(
        'id',
        'name',
        'name_bn',
        'status',
        'final_status',
        'type_key',
        'active_menu_for',
        'panel',
        'icon',
        'menu_name',
        'form_url',
        'process_key',
        'ref_fields',
        'suggested_status_json',
        'guideline_details',
        'guideline_file',
        'guideline_status'
    );
}
