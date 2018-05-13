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

use Illuminate\Http\Request;
use Response;

class HomeController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function createCall(Request $request)
    {
        $brand = Brand::where('name', $request->input('brand_name', 'test_brand'))->first();
        if (!isset($brand)) { return Response::json('invalid brand specified', 422); }
        $operation = Operation::where('brand_id', $brand->id)->where('name', $request->input('name', 'credit_check'))->first();
        if (!isset($operation)) { return Response::json('invalid operation specified', 422); }
        $super_call_id = SuperCall::create($request->all()+['operation_id' => $operation->id]);
        return Response::json($super_call_id, 200);
    }
    public function getCall(Request $request, $call_id)
    {
        return Response::json('the status of your call', 200);
    }
    public function listCalls(Request $request)
    {
        return Response::json('listing all calls', 200);
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
        return Response::json('dashboard type info on fuseplug', 200);
    }
    public function appApiDoc(Request $request)
    {
        return Response::json('this is the documentation for the fuseplug api endpoints', 200);
    }
    public function brandInterfaceDoc(Request $request)
    {
        $return_data = ['Brands'=>[]];;
//        $brands = Brand::with('Operations')->with('OperationRules')->get();
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
