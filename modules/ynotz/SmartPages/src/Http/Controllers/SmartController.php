<?php

namespace Ynotz\SmartPages\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SmartController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function buildResponse($view, $args = [])
    {
        // $fragment = $this->request->input('x_fr', 'fp');
        $fragment = $this->request->header('x_fr', 'fp');
        if ($fragment != 'fp') {
            return view($view, $args)->fragment($fragment);
        }
        return view($view, $args);
    }
}
