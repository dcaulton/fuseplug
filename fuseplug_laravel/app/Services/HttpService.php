<?php

namespace App\Services;

use App\Jobs\Http;
use App\Models\SuperCall;
use App\Models\Call;
use App\Models\Brand;
use App\Models\Operation;

class HttpService
{
    public static function processRequest($super_call_id) {
        $target_url = 'http://foaas.com/cool/';
        $super_call = SuperCall::find($super_call_id);
        $call = Call::where('super_call_id', $super_call_id)->orderBy('created_at', 'desc')->first();
        $from = 'Dave';
        $initial_payload_array = json_decode($call->request_data, true);
        if (array_key_exists('from', $initial_payload_array)) {
            $from = rawurlencode($initial_payload_array['from']);
        }
        $target_url .= $from;
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $target_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json",
                "Accept: application/json"
            ));
            $data = curl_exec($ch);
            curl_close($ch);
             
            $call->response_data = $data;
            $call->status_code = 'COMPLETE';
            $call->save();

			// determine the next call and schedule it
			$operation = Operation::where('brand_id', $super_call->operation_id);
			$queue_name = env('RABBITMQ_QUEUE', 'fuseplug');
			if (isset($operation->queue)) {
				$queue_name = $operation->queue;
			}
			$call = $super_call->get_next_call();
			if ($call) {
				HttpGet::dispatch($super_call_id)->onQueue($queue_name)->onConnection('rabbitmq');
			}

        } catch (\Exception $e) {
            echo "caught exception in Http Service: " . $e->getMessage() . "\n";
            $super_call->status = 'FAILED';
            $super_call->save();
        }
    }
}
