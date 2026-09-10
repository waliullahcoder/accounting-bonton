<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['client_id', 'software_type', 'pay_type', 'service_charge', 'status', 'created_by', 'updated_by', 'deleted_by'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function months()
    {
        return $this->hasMany(BillingMonth::class, 'service_id');
    }
}
