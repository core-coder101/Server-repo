<?php

namespace App\Http\Controllers;

use App\Models\images;
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
            'TeacherSalary' => 'required',
            'image' => 'required'
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
        $pic = $request->input('image');
        if (isset ($pic)) {
            // Ensure that an image was uploaded
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $pic));

            if ($imageData === false) {
                return response()->json(['success' => false , 'message' => 'Failed to decode image data'], 400);
            }
            $mimeType = mime_content_type($pic);

            $extension = image_type_to_extension(exif_imagetype($pic));

            $filename = uniqid() . $extension; // You can adjust the filename extension based on the image format

            $storagePath = 'images/';

            $savePath = public_path($storagePath . $filename);
            if (file_put_contents($savePath, $imageData) === false) {
                return response()->json(['success' => false , 'message' => 'Failed to save image file'], 500);
            }

            // Assuming you want to associate the image path with the newly created record
            $image = new images();
            $image->UsersID = $userId;
            $image->ImageName = $storagePath . $filename; // Store the image path
            $image->save();
        }
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
