<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    public static function create($super_call, $operation_action, $input_data) {
        $call = new Call;
        $call->operation_action_id = $operation_action->id;
        $call->super_call_id = $super_call->id;
        $call->request_data = $input_data;
        $call->status_code = 'CREATED';
        if (!$call->save()) {
            throw new \Exception('error creating call');
        }
        return $call->id;
    }
}
