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
    public function get_status_object() {
        $return_obj = Array();
        $operations = Operation::where('brand_id', $this->id)->get();
        foreach ($operations as $operation) {
            $return_obj[$operation->name] = 'awesome';
        }
        return $return_obj;
    }
}
