<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Operation;
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
        return Response::json('you just made a call', 200);
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
        $brands = Brand::with('Operations')->get();
        return Response::json($brands, 200);
    }
}
