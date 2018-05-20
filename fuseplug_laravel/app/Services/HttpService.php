<?php

namespace App\Services;

use App\Jobs\HttpJob;
use App\Models\SuperCall;
use App\Models\Call;
use App\Models\Brand;
use App\Models\DataMapping;
use App\Models\Operation;
use App\Models\OperationAction;
use App\Models\OperationRule;

class HttpService
{
    public static function doGetRequest($action, $call) {
        $data_mapping = DataMapping::where('operation_action_id', $action->id)
            ->where('object_type_being_created', 'url')
            ->first();
        if ($data_mapping) {
            $target_url = $data_mapping->transform($call->request_data, $action);
        } 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $target_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Accept: application/json"
        ));

        $debug_data = Array();
        $debug_data["called_url"] = $target_url;
        $debug_data["payload_to_url"] = '';

        $data = curl_exec($ch);

        $info = curl_getinfo($ch);
        $debug_data["response_code"] = $info['http_code'];
        $call->debug_info = json_encode($debug_data);

        curl_close($ch);
        return $data;
    }

    public static function doPostRequest($action, $call) {
        $url_data_mapping = DataMapping::where('operation_action_id', $action->id)
            ->where('object_type_being_created', 'url')
            ->first();
        if ($url_data_mapping) {
            $target_url = $url_data_mapping->transform($call->request_data, $action);
        } 
        $payload_data_mapping = DataMapping::where('operation_action_id', $action->id)
            ->where('object_type_being_created', 'payload')
            ->first();
        if ($payload_data_mapping) {
            $payload = $payload_data_mapping->transform($call->request_data, $action);
        } 
        $payload=json_decode($payload);
        $curl = curl_init($target_url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payload));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Accept: application/json"
        ));

        $debug_data = Array();
        $debug_data["called_url"] = $target_url;
        $debug_data["payload_to_url"] = $payload;

        $response = curl_exec($curl);

        $info = curl_getinfo($curl);
        $debug_data["response_code"] = $info['http_code'];
        $call->debug_info = json_encode($debug_data);

        curl_close($curl);
        return $response;
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
                $data = self::doPostRequest($action, $call);
            }
             
            $call->response_data = $data;
            $call->status_code = 'COMPLETE';
            $call->save();

			// determine the next call and schedule it
			$call = $super_call->get_next_call();
			if ($call) {
				HttpJob::dispatch($super_call_id)->onQueue($queue_name)->onConnection('rabbitmq');
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
