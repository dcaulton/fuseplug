<?php

namespace App\Models;

use App\Models\Call;
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
    //
    public static function create($data, $operation_id) {
        $super_call = new SuperCall;
//        $super_call->id = gen_uuid();
        $super_call->operation_id = $operation_id;
        $super_call->initial_payload = json_encode($data);
//        $super_call->initial_payload = $data;
        if (!$super_call->save()) {
            throw new \Exception('error creating supercall');
        }
        return $super_call->id;
    }

    public function get_summary() {
        // return a summary of all calls 
        $return_data = Array();
        $this_as_json = json_decode($this);
        $this_as_json->calls = Array();
        $calls = Call::where('super_call_id', $this->id)->orderBy('updated_at')->get();
        foreach ($calls as $call) {
            array_push($this_as_json, json_decode($call));
        }
        array_push($return_data, $this_as_json);
        return $return_data;
    }
}
