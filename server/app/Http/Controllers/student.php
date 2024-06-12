<?php

namespace App\Http\Controllers;

use App\Models\classes;
use App\Models\images;
use App\Models\parents;
use App\Models\students;
use Illuminate\Http\Request;
use Validator;
use Auth;
use App\Models\users;
use App\Models\teachers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class student extends Controller
{
    public function CreateStudent(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'userName' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'StudentDOB' => 'required|date',
            'StudentGender' => 'required|string',
            'StudentCNIC' => 'required|string|max:255',
            'StudentClassID' => 'required|string',
            'StudentPhoneNumber' => 'required|string|max:125',
            'StudentHomeAddress' => 'required|string|max:255',
            'StudentReligion' => 'required|string|max:255',
            'StudentMonthlyFee' => 'required|max:255',
            'FatherName' => 'required|string|max:255',
            'MotherName' => 'required|string|max:255',
            'GuardiansCNIC' => 'required|string|max:255',
            'GuardiansPhoneNumber' => 'required|string|max:255',
            'GuardiansPhoneNumber2' => 'string|max:255',
            'HomeAddress' => 'required|string|max:255',
            'GuardiansEmail' => 'required|email|max:255',
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

        if ($user->role == "Admin") {
            $email = $request->input('email');
            $ClassID = $request->input('StudentClassID');
            $password = Str::random(12);
            $BcryptPassword = bcrypt($password);
            $role = "Student";
            $user = users::create([
                'name' => $request->input('name'),
                'userName' => $request->input('userName'),
                'role' => $role,
                'email' => $request->input('email'),
                'password' => $BcryptPassword
            ]);
            $userId = $user->id;
            $pic = $request->input('image');
            if (isset($pic)) {
                // Ensure that an image was uploaded
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $pic));

                if ($imageData === false) {
                    return response()->json(['success' => false, 'message' => 'Failed to decode image data'], 400);
                }
                $mimeType = mime_content_type($pic);

                $extension = image_type_to_extension(exif_imagetype($pic));

                $filename = uniqid() . $extension; // You can adjust the filename extension based on the image format

                $storagePath = 'images/';

                $savePath = public_path($storagePath . $filename);
                if (file_put_contents($savePath, $imageData) === false) {
                    return response()->json(['success' => false, 'message' => 'Failed to save image file'], 500);
                }

                // Assuming you want to associate the image path with the newly created record
                $image = new images();
                $image->UsersID = $userId;
                $image->ImageName = $storagePath . $filename; // Store the image path
                $image->save();
            }
            $class = classes::find($ClassID);
            $StudentTeacherID = $class->ClassTeacherID;
            $student = students::create([
                'StudentUserID' => $userId,
                'StudentClassID' => $request->input('StudentClassID'), // student Class ID
                'StudentDOB' => $request->input('StudentDOB'),
                'StudentGender' => $request->input('StudentGender'),
                'StudentCNIC' => $request->input('StudentCNIC'),
                'StudentPhoneNumber' => $request->input('StudentPhoneNumber'),
                'StudentHomeAddress' => $request->input('StudentHomeAddress'),
                'StudentReligion' => $request->input('StudentReligion'),
                'StudentMonthlyFee' => $request->input('StudentMonthlyFee'),
                'StudentTeacherID' => $StudentTeacherID
            ]);
            $StudentID = $student->id;
            $parent = parents::create([
                'StudentID' => $StudentID,
                'FatherName' => $request->input('FatherName'), // student Class ID
                'MotherName' => $request->input('MotherName'),
                'GuardiansCNIC' => $request->input('GuardiansCNIC'),
                'GuardiansPhoneNumber' => $request->input('GuardiansPhoneNumber'),
                'GuardiansPhoneNumber2' => $request->input('GuardiansPhoneNumber2'),
                'HomeAddress' => $request->input('HomeAddress'),
                'GuardiansEmail' => $request->input('GuardiansEmail')
            ]);
            if ($parent) {
                $Url = 'http://localhost:3000/login?email=' . urlencode($email) . '&password=' . urlencode($password);
                $details = [
                    'title' => 'Successfully Added a new Student',
                    'body' => 'To login into your Student account please enter the following password',
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
            } else {
                $response = [
                    'success' => false,
                    'message' => "Sorry! Something went wrong. Please try again later."
                ];
                $user = users::find($userId);
                $user->delete();
                return response()->json($response);
            }
        } else {
            $response = [
                'success' => false,
                'message' => "Only Admin Can Create Student"
            ];
            return response()->json($response);
        }
    }
    public function GetStudentInformation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ClassRank' => 'required',
            'ClassName' => 'required',
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response);
        } else {
            $ClassRank = $request->input('ClassRank');
            $ClassName = $request->input('ClassName');
            $Class = classes::where('ClassRank', $ClassRank)
                ->where('ClassName', $ClassName)
                ->first();
            if ($Class) {
                $students = $Class->students()->with('users.images', 'parents')->get();
                if ($students) {
                    foreach ($students as $student) {
                        if (isset($student->users->images[0])) {
                            $imgPath = $student->users->images[0]->ImageName;
                            $data = base64_encode(file_get_contents(public_path($imgPath)));
                            $student->users->images[0]->setAttribute('data', $data);
                        }
                    }
                    return response()->json(['success' => true, 'data' => $students]);
                } else {
                    return response()->json(['success' => false, 'message' => 'Student not found']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Class not found']);
            }
        }
    }
    public function Delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID' => 'required',
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response);
        } else {
            $user = $request->user();

            if ($user->role == "Admin") {
                $ID = $request->input('ID');
                $student = students::find($ID);
                
                if ($student) {
                    $student->delete();
                        $response = [
                            'success' => true,
                            'data' => "Successfully deleted"
                        ];
                        return response()->json($response);
                } else {
                    return response()->json(['success' => false, 'message' => 'Class not found']);
                }
            } else {
                $response = [
                    'success' => false,
                    'message' => "Only Admin Can Delete Class"
                ];
                return response()->json($response);
            }
        }
    }
}
