<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Auth;
use App\Models\users;
use App\Models\teachers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class teacher extends Controller
{
    public function CreateTeacher(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'userName' => 'required|string|max:255',
            'email' => 'required|email',
            'TeacherDOB' => 'required|date',
            'TeacherCNIC' => 'required|string',
            'TeacherPhoneNumber' => 'required|string|max:255',
            'TeacherHomeAddress' => 'required',
            'TeacherReligion' => 'required',
            'TeacherSalary' => 'required'
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response);
        }

        $user = $request->user();

        if($user->role == "Admin"){
        $email = $request->input('email');
        $password = Str::random(12);
        $BcryptPassword = bcrypt($password);
        $role = "Teacher";
        $user = users::create([
            'name' => $request->input('name'),
            'userName' => $request->input('userName'),
            'role' => $role,
            'email' => $request->input('email'),
            'password' => $BcryptPassword
        ]);
        $userId = $user->id;
        $teacher = teachers::create([
            'TeacherUserID' => $userId,
            'TeacherDOB' => $request->input('TeacherDOB'),
            'TeacherCNIC' => $request->input('TeacherCNIC'),
            'TeacherPhoneNumber' => $request->input('TeacherPhoneNumber'),
            'TeacherHomeAddress' => $request->input('TeacherHomeAddress'),
            'TeacherReligion' => $request->input('TeacherReligion'),
            'TeacherSalary' => $request->input('TeacherSalary')
        ]);
        if($teacher){
            $Url = 'http://localhost:3000/login?email=' . urlencode($email) . '&password=' . urlencode($password);
                $details = [
                    'title' => 'Successfully Added a new teacher',
                    'body' => 'To login into your teacher account please enter the following password',
                    'password' => $password,
                    'Url' => $Url
                ];
                try {
                    Mail::to($email)->send(new \App\Mail\passwordSender($details));
                    $response = [
                        'success' => true,
                        'message' => "Please check your email for Activation of account."
                    ];
                    return response()->json($response);
                } catch (\Exception $e) {
                    $response = [
                        'success' => true,
                        'message' => "Failed to send email. Please try again later."
                    ];
                    return response()->json($response);
                }
        }
        else{
            $response = [
                'success' => false,
                'message' => "Sorry! Something went wrong. Please try again later."
            ];
            $user = users::find($userId);
            $user->delete();
            return response()->json($response);
        }
    }
    else{
        $response = [
            'success' => false,
            'message' => "Only Admin Can Create Teacher"
        ];
        return response()->json($response);
    }
    }
    public function GetTeacher(){
        $teachers = teachers::with('user')->get();
        if($teachers){
        $response = [
            'success' => true,
            'data' => $teachers
        ];
        return response()->json($response); 
        }       
    }
}
