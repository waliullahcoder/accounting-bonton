<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminSetting extends Model
{
    use HasFactory;
    protected $fillable = ['logo', 'favicon', 'title', 'footer_text', 'primary_color', 'secondary_color', 'collection_head', 'facebook', 'twitter', 'linkedin', 'whatsapp', 'google'];

    public function coa()
    {
        return $this->belongsTo(CoaSetup::class, 'collection_head');
    }
}
