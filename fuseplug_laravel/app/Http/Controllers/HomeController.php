<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Operation;
use App\Models\OperationRule;
use App\Models\OperationAction;
use App\Models\SuperCall;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Jobs\HttpJob;

use Illuminate\Http\Request;
use Response;

class HomeController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function createCall(Request $request)
    {
/*
This is what good post datalooks like for http_get:
[
  {"payload": 
    {
     "from": "Your Mama"
    }
 },
 {"control": 
 	{"brand": "test_brand",
 	 "operation": "credit_check_laravel"
 	}
 	
 }
]
*/
        $payload = $request->all()[0]['payload'];
        $control_data = $request->all()[1]['control'];
        $brand_name = 'test_brand';
        if (isset($control_data['brand'])) {
            $brand_name = $control_data['brand'];
        } 
        $brand = Brand::where('name', $brand_name)->first();
        if (!isset($brand)) { return Response::json('invalid brand specified', 422); }

        $operation_name = 'credit_check_laravel';
        if (isset($control_data['operation'])) {
            $operation_name = $control_data['operation'];
        } 
        $operation = Operation::where('brand_id', $brand->id) 
            ->where('name', $operation_name)
            ->first();
        if (!isset($operation)) { return Response::json('invalid operation specified', 422); }

        $queue_name = env('RABBITMQ_QUEUE', 'fuseplug');  
        if (isset($operation->queue)) {
            $queue_name = $operation->queue;
        }

        $get_parameters = $request->query();

        $super_call_id = SuperCall::create($payload, $get_parameters, $operation->id);
        $super_call = SuperCall::find($super_call_id);
        $call = $super_call->get_next_call();
        if ($call) {
            HttpJob::dispatch($super_call_id)->onQueue($queue_name)->onConnection('rabbitmq');
        }
        return Response::json($super_call_id, 202); // return a 202 Accepted indicating it's going to run, check back later
    }

    public function getCall(Request $request, $call_id)
    {
        $super_call = SuperCall::findOrFail($call_id);
        $return_data = $super_call->get_summary();
        return Response::json($return_data, 200);
    }
    public function listCalls(Request $request)
    {
        $super_calls = SuperCall::all();
        return Response::json($super_calls, 200);
    }

    // This is a hack.  It does the same thing as a post to createCall
    // It's needed because I'm sure some brand will have a problem creating a POST call
    //   as our operation architecture will require
    public function callback(Request $request)
    {
  
        return Response::json('you just made a call', 200);
    }
    public function appStatus(Request $request)
    {   
        $status_obj = ["uptime" => "54 days",
            "software_branch"=> "release.0.0.65",
            "software_commit_hash"=> "a9604030d2cfbf792dbd078b71d3979eec737b1c",
            "overall_status" => "great",
            "cronjobs" => "22",
            "operations" => 35];
        $status_obj['brands'] = [];
        $brands = Brand::all();
        foreach ($brands as $brand) {
            $status_obj['brands'][$brand->name] = $brand->get_status_object();
        }
        return Response::json($status_obj, 200);
    }
    public function appApiDoc(Request $request)
    {
        $endpoints = [
            '/call'=> [
                'POST' => "how to initially request a call.  See /brand-interfaces-doc for details on your specific brand and service.",
                'GET' => 'list all active calls'
            ],
            '/call/{call id}'=> [
                'GET' => "check on the status of a call.  Will supply data or a url to the data if it is ready."
            ],
            '/app-status'=> [
                'GET' => "check on the status of the system.  Will list status and any known issues with your brands and services"
            ],
            '/app-api-doc'=> [
                'GET' => "You are here.  Documentation on the fuseplug application"
            ],
            '/brand-interfaces-doc'=> [
                'GET' => "gets detailed information on the brands and services available to you"
            ],
        ];
        return Response::json($endpoints, 200);
    }
    public function brandInterfaceDoc(Request $request)
    {
        $return_data = ['Brands'=>[]];;
        $brands = json_decode(Brand::all());
        foreach ($brands as $brand) {
            $operations = json_decode(Operation::where('brand_id', $brand->id)->get());
            $brand->operations = Array();
            foreach ($operations as $operation) {
                $operation_rules = json_decode(OperationRule::where('operation_id', $operation->id)->get());
                $operation->operation_rules = Array();
                foreach ($operation_rules as $operation_rule) {
   
                    $operation_actions = json_decode(OperationAction::where('operation_rule_id', $operation_rule->id)->get());
                    $operation_rule->operation_actions = Array();
                    foreach ($operation_actions as $operation_action) {
                        array_push($operation_rule->operation_actions, $operation_action);
                    }
                    array_push($operation->operation_rules, $operation_rule);
                }
                array_push($brand->operations, $operation);
            }
        }
        return Response::json($brands, 200);
    }
}
