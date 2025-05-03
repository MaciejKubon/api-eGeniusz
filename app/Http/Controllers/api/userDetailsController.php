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

    public function userDetails(){
        $user = auth()->user();
        $path = Storage::url($user->avatar);
        try {
            if($user->role=="teacher"){
                $userDetals = [
                    "firstName"=> $user->first_name,
                    "lastName"=> $user->last_name,
                    "birthday"=>$user->birthday,
                    "description"=> $user->description,
                    "avatar"=>$path
                ];}
            else if($user->role=="student"){
                $userDetals = [
                    "firstName"=> $user->first_name,
                    "lastName"=> $user->last_name,
                    "birthday"=>$user->birthday,
                    "avatar"=>$path
                ];
            }
            return response()->json([
                'message' => 'sucess',
                'userDetails' => $userDetals,
            ], 200);
        }catch (\Exception $e) {
            return response()->json([
                'message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }

    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        if(!auth()->user()->can('edit_user_details'))
            abort(403);
        $validator = Validator::make($request->all(), [
            "firstName" => "required|min:2|max:255",
            "lastName" => "required|min:2|max:255",
            "birthday" => "date_format:Y-m-d",
            "description"=>"max:1000",
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Nie udało się zaktualizować profil',
                'error' => $validator->errors(),
            ], 400);
        }
        try{
            User::where('id',$user->id)->update([
                'first_name'=>$request->firstName,
                'last_name'=>$request->lastName,
                'birthday'=>$request->birthday,
                'description'=>$request->description
            ]);
            return response()->json([
                'message' => 'Profil został zaktualizowany!',

            ], 200);
        }catch(\Exception $e) {
            return response()->json(['
                message' => 'Nie udało się zaktualizować profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getUserAvatar(){
        $user = auth()->user();
        $path = Storage::url($user->avatar);
        try{
        return response()->json([
            'message' => 'sucess',
            'avatar' => $path,
            ], 200);
        }catch(\Exception $e) {
            return response()->json([
                'message' => 'Nie udało się zaktualizować profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function setUserAvatar(Request $request){
        $user = auth()->user();
        $path = $request->file('image')->store('images','public');
        try{
            User::where('id',$user->id)->update([
                'avatar' => $path]);
            return response()->json([
                'message' => 'Profil został zaktualizowany!',
                'avatar' => $path,
            ], 200);
        }catch(\Exception $e) {
            return response()->json(['
                message' => 'Nie udało się zaktualizować profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteUserAvatar(){
        $user = auth()->user();
        $path = '/storage/images/avatar.png';
        try{
            User::where('id',$user->id)->update([
                'avatar' => $path,
            ]);
            return response()->json([
                'message' => 'Profil został zaktualizowany!',
                'avatar' => $path,
            ], 200);
        }catch(\Exception $e) {
            return response()->json(['
                message' => 'Nie udało się zaktualizować profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
