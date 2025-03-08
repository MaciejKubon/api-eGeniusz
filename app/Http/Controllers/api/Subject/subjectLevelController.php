<?php

namespace App\Http\Controllers\api\Subject;

use App\Http\Controllers\Controller;
use App\Models\subjectLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class subjectLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->can('show_subject_level'))
            abort(403);
        try{
            return response()->json([
                'message' => 'sucess',
                'subjectLevel' => subjectLevel::all(),
            ], 200);
        }
        catch(\Exception $e){
            return response()->json(['
                message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$request->user()->can('create_subject_level'))
            abort(403);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:50|unique:subject_levels',
        ],[
            'name.required' => 'Please enter subject level name.',
            'name.max' => 'Name cannot be more than 255 characters.',
            'name.min' => 'Name cannot be less than 2 characters.',
            'name.unique' => 'This name is already taken.',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'error'  => $validator->errors(),
            ], 400);
        }
        try{
            subjectLevel::create([
                'name' => $request->name,
            ]);
            return response()->json([
                'message' => 'sucess',
            ], 201);
        }
        catch(\Exception $e){
            return response()->json(['
                message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(subjectLevel $subjectLevel)
    {
        if (!auth()->user()->can('show_subject_level'))
            abort(403);
        try{
            return response()->json([
                'message' => 'sucess',
                'subjectLevel' => $subjectLevel,
            ], 200);
        }
        catch(\Exception $e){
            return response()->json(['
                message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, subjectLevel $subjectLevel)
    {
        if (!$request->user()->can('update_subject_level'))
            abort(403);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:50|unique:subject_levels',
        ],[
            'name.required' => 'Please enter subject level name.',
            'name.max' => 'Name cannot be more than 255 characters.',
            'name.min' => 'Name cannot be less than 2 characters.',
            'name.unique' => 'This name is already taken.',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'error'  => $validator->errors(),
            ], 400);
        }
        try{
            $subjectLevel->update($request->all());
            return response()->json([
                'message' => 'sucess',
            ], 200);
        }
        catch(\Exception $e){
            return response()->json(['
                message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(subjectLevel $subjectLevel)
    {
        if (!auth()->user()->can('delete_subject_level'))
            abort(403);
        try{
            $subjectLevel->delete();
            return response()->json([
                'message' => 'sucess',
            ], 200);
        }
        catch(\Exception $e){
            return response()->json(['
                message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
