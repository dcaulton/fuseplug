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

use Illuminate\Support\Facades\Log;
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
        $curl = curl_init($target_url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
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

    public static function getQueueName($operation, $action, $super_call) {
        $queue_name = env('RABBITMQ_QUEUE', 'fuseplug');
        if (isset($operation->queue)) {
            $queue_name = $operation->queue;
        }
        if (isset($action->queue)) {
            $queue_name = $action->queue;
        }
        $sc_payload = json_decode($super_call->initial_payload);
        if (array_key_exists('queue_name', $sc_payload->control_data)) {
            $queue_name = $sc_payload->control_data->queue_name;
        }
        return $queue_name;
    }

    public static function processRequest($super_call_id) {
        $super_call = SuperCall::find($super_call_id);
        $call = Call::where('super_call_id', $super_call_id)->orderBy('created_at', 'desc')->first();
        $action = OperationAction::find($call->operation_action_id);
        $rule = OperationRule::find($action->operation_rule_id);
        try {
            if ($action->http_verb == 'GET') {
                $data = self::doGetRequest($action, $call);
            } elseif ($action->http_verb == 'POST') {
                $data = self::doPostRequest($action, $call);
            }
             
            if (strlen($data) > 4096) {
                // TODO save this to a different table.  for now just truncate, this JSON will never parse again though!
                $data = substr($data, 0, 4095);
            }
            $call->response_data = $data;
            $call->status_code = 'COMPLETE';
            $call->save();

			// determine the next call and schedule it
			$next_call = $super_call->get_next_call();
			if ($next_call) {
                $operation = Operation::find($rule->operation_id);
                $queue_name = self::getQueueName($operation, $action, $super_call);
				HttpJob::dispatch($super_call_id)->onQueue($queue_name)->onConnection('rabbitmq');
			}

        } catch (\Exception $e) {
            $super_call->status = 'FAILED';
            $super_call->save();
            $error_messages = [$e->getMessage()];
            $error_messages = array_merge($error_messages, $e->getTrace());
            $error_messages = json_encode($error_messages);
            $error_messages = substr($error_messages, 0, 4000);
            if ($call) {
                $call->error_messages = $error_messages;
                $call->status_code = 'FAILED';
                $call->save();
            }
        }
    }
}
