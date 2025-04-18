<?php

namespace App\Http\Controllers\api;

use App\Models\user;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class userDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    public function show(user $user)
    {
        try {
            return response()->json([
                'message' => 'sucess',
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['
                message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, user $user)
    {
        if(!auth()->user()->can('edit_user_details'))
            abort(403);
        if($request->user()->id != $user->id || $request->user()->role !="admin")
            abort(403);
        $validator = Validator::make($request->all(), [
            "first_name" => "required|min:2|max:255",
            "last_name" => "required|min:2|max:255",
            "birthday" => "format:d-m-Y",
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'error',
                'error' => $validator->errors(),
            ], 400);
        }
        try{
            $user ->update($request->all());
            return response()->json([
                'message' => 'sucess',
            ], 200);
        }catch(\Exception $e) {
            return response()->json(['
                message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getUserAvatar(Request $request){
        $teacher = $request->user()->load('teacher');
        $teacher = $teacher['teacher'];
        $path = $teacher['imgPath'];
        $imageUrl = Storage::url($path);
        return response()->json(['imageUrl' => $imageUrl]);
    }

}
