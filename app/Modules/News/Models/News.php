<?php

namespace App\Modules\News\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'news';
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'status',
        'publish_date',
        'post_author',
        'post_date_gmt',
        'post_excerpt',
        'post_status',
        'comment_status',
        'ping_status',
        'post_password',
        'post_name',
        'to_ping',
        'pinged',
        'post_modified',
        'post_modified_gmt',
        'post_content_filtered',
        'post_parent',
        'menu_order',
        'post_type',
        'post_mime_type',
        'comment_count',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'old_data',
        'bulletin_master_id'
    ];
}
