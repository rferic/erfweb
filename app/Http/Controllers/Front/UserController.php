<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function getEmailIsFree ( Request $request )
    {
        $validator = Validator::make($request->all(), ['email' => 'required|unique:users,email']);
        return Response::json(['result' => !$validator->fails()]);
    }
}
