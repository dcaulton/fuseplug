<?php

namespace App\Services;

use App\Jobs\Http;
use App\Models\SuperCall;
use App\Models\Call;
use App\Models\Brand;
use App\Models\Operation;
use App\Models\OperationAction;
use App\Models\OperationRule;

class HttpService
{
    public static function doGetRequest($action, $call) {
        $target_url = 'http://foaas.com/cool/';
        $from = 'Dave';
        $initial_payload_array = json_decode($call->request_data, true);
        if (array_key_exists('from', $initial_payload_array)) {
            $from = rawurlencode($initial_payload_array['from']);
        }
        $target_url .= $from;
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
        return $data;
    }

    public static function doPostRequest($action, $call) {
        return 'doPostRequest is not yet implemented';
    }

    public static function getQueueName($operation, $action) {
        $queue_name = env('RABBITMQ_QUEUE', 'fuseplug');
        if (isset($operation->queue)) {
            $queue_name = $operation->queue;
        }
        if (isset($action->queue)) {
            $queue_name = $action->queue;
        }
        return $queue_name;
    }

    public static function processRequest($super_call_id) {
        $super_call = SuperCall::find($super_call_id);
        $call = Call::where('super_call_id', $super_call_id)->orderBy('created_at', 'desc')->first();
        $action = OperationAction::find($call->operation_action_id);
        $rule = OperationRule::find($action->operation_rule_id);
        $operation = Operation::find($rule->operation_id);
        $queue_name = self::getQueueName($operation, $action);
        try {
            if ($action->http_verb == 'GET') {
                $data = self::doGetRequest($action, $call);
            } else {
                $data = self::doPostRequest();
            }
             
            $call->response_data = $data;
            $call->status_code = 'COMPLETE';
            $call->save();

			// determine the next call and schedule it
			$call = $super_call->get_next_call();
			if ($call) {
				HttpGet::dispatch($super_call_id)->onQueue($queue_name)->onConnection('rabbitmq');
			}

        } catch (\Exception $e) {
            $super_call->status = 'FAILED';
            $super_call->save();
            $call->error_messages = $e->getMessage();
            $call->status_code = 'FAILED';
            $call->save();
        }
    }
}
