<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Auth;
use App\Models\Admin;

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
        if($request->role == "Admin"){
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = Admin::create($input);

        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['Admin_Name'] = $user->name;

        $response = [
            'success' => true,
            'data' => $success
        ];
        return response()->json($response, 200);
    }
}






    public function login(Request $request)
    {
        if($request->role == "Admin"){
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $admin = Auth::guard('admin')->user();

            $success['token'] = $admin->createToken('MyApp')->plainTextToken;
            $success['Admin_name'] = $admin->name;

            return response()->json(['success' => true, 'data' => $success], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'NoLogin_Unauthorized'], 400);
        }
    }
    }
}
