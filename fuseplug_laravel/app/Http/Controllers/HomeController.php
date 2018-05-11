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
        return Response::json('im surprised to hear that', 200);
    }
    public function appApiDoc(Request $request)
    {
        dd('mamasita');
        return Response::json('oh really', 200);
    }
}
