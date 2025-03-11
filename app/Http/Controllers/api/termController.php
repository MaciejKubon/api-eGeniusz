<?php

namespace App\Http\Controllers\api;

use App\Models\term;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class termController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->can('show_admin_term'))
            abort(403);
        $terms = term::all();
        $termArray = array();
        foreach ($terms as $term) {
            $termArray[] = $this -> termDetails($term);
        }
        try{
            return response()->json([
                'message' => 'sucess',
                'term' => $termArray,
            ], 200);
        }
        catch(\Exception $e){
            return response()->json(['
                message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function showTeacherTerms(User $user){
        if (!auth()->user()->can('show_all_teacher_term'))
            abort(403);
        $terms = $user ->terms;
        $termArray = array();
        foreach ($terms as $term) {
            $termArray[] = $this -> termDetails($term);
        }
        try{
            return response()->json([
                'message' => 'sucess',
                'term' => $termArray
            ]);}
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
        if (!auth()->user()->can('create_term'))
            abort(403);
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date_format:Y-m-d H:i:s',
            'end_date' => 'required|date_format:Y-m-d H:i:s||after:start_date',
        ],[
            'start_date.required' => 'Start date is required',
            'start_date.date_format' => 'Invalid date format',
            'end_date.required' => 'End date is required',
            'end_date.date_format' => 'Invalid date format',
            'end_date.after' => 'End date must be after start date'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'error'  => $validator->errors(),
            ], 400);
        }
        $teacher = $request->user();
        $termList = $teacher -> terms;
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];
        foreach ($termList as $term) {
            if((($term['start_date']>=$start_date) && ($term['end_date']<=$end_date))||
                (($term['start_date']<=$start_date) && ($term['end_date']>=$end_date))||
                (($term['start_date']>=$start_date) && ($term['end_date']<=$end_date))||
                (($term['start_date']<=$start_date) && ($term['end_date']>=$end_date))){
                return response()->json([
                    'message' => 'error',
                    'error' => "Data koliduje z innym terminem"
                ], 400);
            }
        }
        try {
            term::create([
                'teacher_id' => $teacher -> id,
                'start_date' => $start_date,
                'end_date' => $end_date
            ]);
            return response()->json([
                'message' => 'sucess',
            ], 201);
        }catch(\Exception $e){
            return response()->json(['
                message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(term $term)
    {
        try {
            return response()->json([
                'message' => 'sucess',
                'term' => $this->termDetails($term)
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, term $term)
    {
        if (!auth()->user()->can('edit_term'))
            abort(403);
        if($request->user()->id != $term->teacher_id)
            abort(403);
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date_format:Y-m-d H:i:s',
            'end_date' => 'required|date_format:Y-m-d H:i:s||after:start_date',
        ],[
            'start_date.required' => 'Start date is required',
            'start_date.date_format' => 'Invalid date format',
            'end_date.required' => 'End date is required',
            'end_date.date_format' => 'Invalid date format',
            'end_date.after' => 'End date must be after start date'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'error'  => $validator->errors(),
            ], 400);
        }
        try {
            $term->update($request->all());
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
    public function destroy(term $term)
    {
        if (!auth()->user()->can('delete_term'))
            abort(403);
        $teacher = user::find(auth()->user());
        if($teacher->role == "teacher" && $term->teacher_id != $teacher->id){
            abort(403);
        }
        try{
            $term->delete();
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


    private function termDetails(term $term){
        $teacher = ['id' => $term->user->id,
            'firstName'=> $term->user->firstName,
            'lastName'=>$term->user->lastName];
        try {
            $terms = [
                'id' => $term['id'],
                'start_date' => $term['start_date'],
                'end_date' => $term['end_date'],
                'teacher' => $teacher,
            ];
            return $terms;
        }catch(\Exception $e){
            return response()->json([
                'message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
