<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Brand;
use App\Models\OperationRule;

class Operation extends Model
{
    //
    public function Brand() {
        return $this->belongsTo(Brand::class);
    }
    public function OperationRules() {
        return $this->hasMany(OperationRule::class);
    }
}
