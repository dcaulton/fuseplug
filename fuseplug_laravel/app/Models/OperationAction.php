<?php

namespace App\Models;

use App\Models\DataMapping;
use Illuminate\Database\Eloquent\Model;

class OperationAction extends Model
{
    public function get_url() {
        $data_mapping = DataMapping::where('operation_action_id', $this->id)->first();
        if (!isset($data_mapping)) {
            throw new \Exception('cannot find data mapping for operation action ' . $this->id);
        }
        return $this->template;
    }
}
