<?php

namespace App\Models;
use App\Models\Operation;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    //
    public function Operations() {
        return $this->hasMany(Operation::class);
    }
}
