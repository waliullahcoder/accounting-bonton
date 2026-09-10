<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoaSetup extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['company_id', 'parent_id', 'head_code', 'head_name', 'transaction', 'general', 'head_type', 'status', 'created_by', 'updated_by', 'deleted_by'];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->withTrashed();
    }

    public function scopeRoot($query)
    {
        $query->whereNull('parent_id')->with(['children']);
    }

    public function children()
    {
        return $this->hasMany($this, 'parent_id')->orderBy('head_code', 'asc')->with(['children']);
    }

    public function parent()
    {
        return $this->belongsTo($this, 'parent_id');
    }
}
