<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\classes;
use App\Models\users;
use App\Models\teachers;


class Classess extends Controller
{
    public function CreateClass(Request $request){
        $validator = Validator::make($request->all(), [
            'ClassName' => 'required|string|max:255',
            'ClassRank' => 'required|string|max:255',
            'ClassFloor' => 'required',
            'ClassTeacherID' => 'required'
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
            $input = request()->all();
            $class = classes::create($input);
            if($class){
                $response = [
                    'success' => true,
                    'data' => $class
                ];
                return response()->json($response);
            }
            else{
                $response = [
                    'success' => false,
                    'message' => "Cannot create class Successfully"
                ];
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
    public function GetClasses(){
        $classes = classes::all();
        if($classes){
            $response = [
                'success' => true,
                'data' => $classes
            ];
            return response()->json($response);
        }
        else{
            $response = [
                'success' => false,
                'message' => "Cannot Get any Class"
            ];
            return response()->json($response);
        }
    }




    public function Delete(Request $request){
        $validator = Validator::make($request->all(), [
            'ID' => 'required',
            ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response);
        }
        else{
            $user = $request->user();

            if($user->role == "Admin"){
                $ID = $request->input('ID');
                $class = classes::find($ID);
                if ($class) {
                    $class->delete();
                    $classes = classes::all();
                    if($classes){
                        $response = [
                            'success' => true,
                            'data' => $classes
                        ];
                        return response()->json($response);
                    }
                } else {
                    return response()->json(['success' => false, 'message' => 'Class not found']);
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
    }
}
