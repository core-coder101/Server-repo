<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Auth;
use App\Models\users;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'userName' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'role' => 'required'
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = users::create($input);

            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['Name'] = $user->name;
            $success['Role'] = $user->role;

            $response = [
                'success' => true,
                'data' => $success
            ];
            return response()->json($response, 200);
    }






    public function login(Request $request)
    {
            if (Auth::guard('users')->attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::guard('users')->user();

                $success['token'] = $user->createToken('MyApp')->plainTextToken;
                $success['Name'] = $user->name;
                $success['Role'] = $user->role;

                return response()->json(['success' => true, 'data' => $success], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'NoLogin_Unauthorized'], 400);
            }
    }
}
