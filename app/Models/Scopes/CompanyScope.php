<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class CompanyScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */

    public function apply(Builder $builder, Model $model)
    {
        if (auth()->check()) {
            $company_id = Auth::user()->company_id;

            if ($company_id) {
                $tableName = $builder->getModel()->getTable();
                if ($tableName == 'companies') {
                    $builder->where('id', $company_id);
                } elseif (Schema::hasColumn($tableName, 'company_id')) {
                    $builder->where('company_id', $company_id);
                }
            }
        }
    }
}
