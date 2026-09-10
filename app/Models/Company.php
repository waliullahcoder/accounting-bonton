<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id', 'prefix', 'name', 'username', 'email', 'phone', 'fax', 'website', 'vat', 'tin', 'trade_license', 'address', 'logo', 'created_by', 'updated_by', 'deleted_by'];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'company_id');
    }
}
