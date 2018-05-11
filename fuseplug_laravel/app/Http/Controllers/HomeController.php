<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Http\Request;
use Response;

class HomeController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function call(Request $request)
    {
        return Response::json('you just made a call', 200);
    }
    public function callStatus(Request $request)
    {
        return Response::json('the status of your call', 200);
    }
    public function callData(Request $request)
    {
        return Response::json('the data from your call', 200);
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
        return Response::json('all data on all configured brand partner integrations', 200);
    }
}
