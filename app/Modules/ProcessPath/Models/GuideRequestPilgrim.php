<?php

namespace App\Modules\ProcessPath\Models;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\CommonFunction;
use Illuminate\Support\Facades\Auth;


class GuideRequestPilgrim extends Model
{

    protected $table = 'guide_request_pilgrims';
    protected $fillable = array(
        'id',
        'pilgrim_data_list_id',
        'process_list_id',
        'guide_id',
        'tracking_no',
        'pid',
        'status',
        'session_id'
    );

//    public static function boot()
//    {
//        parent::boot();
//        // Before update
//        static::creating(function ($post) {
//            $post->created_by = Auth::user()->id;
//            $post->updated_by = CommonFunction::getUserId();
//        });
//
//        static::updating(function ($post) {
//            $post->updated_by = CommonFunction::getUserId();
//        });
//    }
//
//    public function processlist()
//    {
//        return $this->hasOne(ProcessList::class, 'ref_id', 'id');
//    }
}
