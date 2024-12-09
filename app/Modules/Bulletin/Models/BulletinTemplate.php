<?php

namespace App\Modules\Bulletin\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulletinTemplate extends Model
{
    use HasFactory;

    protected $table = 'haj_bulletin_template';

    protected $fillable = [
        'bulletin_subject',
        'haj_type',
        'bulletin_text',
        'mail_text',
        'status'
    ];



}
