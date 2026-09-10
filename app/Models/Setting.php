<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'primary_mobile',
        'secondary_mobile',
        'primary_email',
        'secondary_email',
        'office_time',
        'address',
        'description',
        'meta_title',
        'meta_keyword',
        'meta_description',
        'meta_image',
        'google_map',
        'favicon',
        'logo',
        'footer_logo',
        'placeholder',
        'facebook_page',
        'facebook_group',
        'youtube',
        'twitter',
        'linkedin',
        'google',
        'whatsapp',
        'instagram',
        'pinterest',
    ];
}
