<?php

namespace App\Http\Controllers\api\Subject;

use App\Models\subject;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class subjectController extends Controller
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
                'subject' => subject::all(),
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
        if (!$request->user()->can('create_subject'))
            abort(403);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:50|unique:subjects',
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
            subject::create([
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
    public function show(subject $subject)
    {
        if (!auth()->user()->can('show_subject'))
            abort(403);
        try{
            return response()->json([
                'message' => 'sucess',
                'subject' => $subject,
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
    public function update(Request $request, subject $subject)
    {
        if (!$request->user()->can('update_subject'))
            abort(403);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:50|unique:subjects',
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
            $subject->update($request->all());
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
    public function destroy(subject $subject)
    {
        if (!auth()->user()->can('delete_subject'))
            abort(403);
        try{
            $subject->delete();
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
