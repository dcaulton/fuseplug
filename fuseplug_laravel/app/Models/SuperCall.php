<?php

namespace App\Models;

use App\Models\Call;
use App\Models\OperationRule;
use App\Models\OperationAction;
use Illuminate\Database\Eloquent\Model;
use Uuid;


function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

class SuperCall extends Model
{
    public static function create($payload, $get_parameters, $operation_id) {
        $super_call = new SuperCall;
        $super_call->operation_id = $operation_id;

        $payload_obj = [];
        $payload_obj['get_parameters'] = $get_parameters;
        foreach (array_keys($payload_obj['get_parameters']) as $get_parm_key) {
            $payload_obj['get_parameters'][$get_parm_key] = 
                rawurldecode($payload_obj['get_parameters'][$get_parm_key]);
        }
        $payload_obj['payload'] = $payload;
        $super_call->initial_payload = json_encode($payload_obj);

        $super_call->status = 'ACTIVE';
        if (!$super_call->save()) {
            throw new \Exception('error creating supercall');
        }
        return $super_call->id;
    }

    public function get_summary() {
        $return_data = Array();
        $this_as_json = json_decode($this);
        $calls = Call::where('super_call_id', $this->id)->orderBy('updated_at')->get();
        array_push($return_data, $this_as_json);
        return $return_data;
    }
    public function get_next_call() {
        $rules = OperationRule::where('operation_id', $this->operation_id)->orderBy('order')->get();
        $calls = Call::where('super_call_id', $this->id)->orderBy('updated_at')->get();
        $called_action_ids = [];
        $last_response = $this->initial_payload;
        foreach ($calls as $call) {
            array_push($called_action_ids, $call->operation_action_id);
            $last_response = $call->response_data;
        }
        foreach ($rules as $rule) {
            if (!$rule->should_be_called($this, $calls)) {
                continue;
            }
            $actions = OperationAction::where('operation_rule_id', $rule->id)->orderBy('order')->get();
            foreach ($actions as $action) {
                if (in_array($action->id, $called_action_ids)) {
                    continue;
                }
                // else we have actions for this rule which haven't been performed yet, create a call for the first one!
                $call_id = Call::create($this, $action, $last_response);
                return $call_id;
            }
        }
        // if no more calls, make this supercall complete, move the last calls payload to final_response
        $this->final_response = $last_response;
        $this->status = 'COMPLETE';
        if (!$this->save()) {
            throw new \Exception('error finalizing supercall');
        }
        return null;
    }
}
