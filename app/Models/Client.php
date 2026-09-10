<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'type',  'company_name', 'phone', 'email', 'coa_setup_id', 'status', 'created_by', 'updated_by', 'deleted_by'];

    public function coa()
    {
        return $this->belongsTo(CoaSetup::class, 'coa_setup_id');
    }

    public function transactions()
    {
        return $this->hasMany(AccountTransactionAuto::class, 'coa_setup_id', 'coa_setup_id');
    }
}
