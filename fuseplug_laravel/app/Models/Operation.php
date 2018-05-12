<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Brand;

class Operation extends Model
{
    //
    public function Brand() {
        return $this->belongsTo(Brand::class);
    }
}
