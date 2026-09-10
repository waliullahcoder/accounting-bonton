<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientCollection extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['serial_no', 'month', 'year', 'date', 'total_bill_amount', 'total_collection_amount', 'created_by', 'updated_by', 'deleted_by'];

    public function list()
    {
        return $this->hasMany(ClientCollectionList::class, 'client_collection_id');
    }

    public function transactions()
    {
        return $this->hasMany(AccountTransaction::class, 'voucher_no', 'serial_no')->where('voucher_type', 'Collection');
    }
}
