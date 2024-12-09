<?php

namespace App\Modules\ProcessPath\Models;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\CommonFunction;
use Illuminate\Support\Facades\Auth;


class PilgrimDataList extends Model
{

    protected $table = 'pilgrim_data_list';
    protected $fillable = array(
        'id',
        'json_boject',
        'status',
        'request_type',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'
    );

    public static function boot()
    {
        parent::boot();
        // Before update
        static::creating(function ($post) {
            $post->created_by = Auth::user() ? Auth::user()->id : 0 ;
            $post->updated_by = CommonFunction::getUserId();
        });

        static::updating(function ($post) {
            $post->updated_by = CommonFunction::getUserId();
        });
    }

    public function processlist()
    {
        return $this->hasOne(ProcessList::class, 'ref_id', 'id');
    }
}
