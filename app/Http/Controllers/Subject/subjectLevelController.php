<?php

namespace App\Http\Controllers\Subject;

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
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:1|unique:subject_levels',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'error'  => $validator->errors(),
            ], 400);
        }
        try {
            subjectLevel::create([
                'name' => $request->name,
            ]);
            return response()->json([
                'message' => 'sucess',
            ], 201);
        }
        catch (\Exception $e) {
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(subjectLevel $subjectLevel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(subjectLevel $subjectLevel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, subjectLevel $subjectLevel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(subjectLevel $subjectLevel)
    {
        //
    }
}
