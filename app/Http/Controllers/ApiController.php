<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index (Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ];

        if (auth()->attempt($credentials)) {
            $token = auth()->User()->createToken('ApiToken')->accessToken;
            return Response::json([
                'Status' => 'Success',
                'Data' => $token
            ]);
        }
        return Response::json([
            'Status' => 'Failed',
            'Message' => 'Wrong Credentials'
        ],404);
    }


    public function details ()
    {
        return Response::json([
            'User' => auth()->user()
        ]);
    }

    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }
}
