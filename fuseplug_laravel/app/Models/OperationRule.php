<?php

namespace App\Models;

use App\Models\Operation;
use Illuminate\Database\Eloquent\Model;

class OperationRule extends Model
{
    //
    public function Operation() {
        return $this->belongsTo(Operation::class);
    }
}
