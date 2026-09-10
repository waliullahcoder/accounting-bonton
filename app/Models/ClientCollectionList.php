<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCollectionList extends Model
{
    use HasFactory;
    protected $fillable = ['client_collection_id', 'client_id', 'service_id', 'month', 'year', 'bill_amount', 'collection_amount'];

    public function collection()
    {
        return $this->belongsTo(ClientCollection::class, 'client_collection_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function service()
    {
        return $this->belongsTo(service::class, 'service_id');
    }
}
