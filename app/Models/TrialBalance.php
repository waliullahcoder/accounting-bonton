<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrialBalance extends Model
{
    protected $table = 'view_trial_balance';

    public function coa_setup()
    {
        return $this->belongsTo(CoaSetup::class, 'coa_setup_id');
    }

    public function parent_head()
    {
        return $this->belongsTo(CoaSetup::class, 'parent_id');
    }
}
