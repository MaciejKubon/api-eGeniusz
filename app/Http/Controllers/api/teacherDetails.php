<?php

namespace App\Http\Controllers\api;

use App\Models\classes;
use App\Models\lesson;
use App\Models\subject;
use App\Models\subjectLevel;
use App\Models\term;
use App\Models\user;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class teacherDetails extends Controller
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
    public function create()
    {
        //
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
    public function show(user $user)
    {
        $lessons = $user->lesson;
        $lessonArr = array();
        foreach ($lessons as $lesson) {
            $lesson = $lesson->load('subject', 'subjectLevel');
            $lessonArr[] = [
                'id' => $lesson->id,
                'subject' => $lesson->subject,
                'subjectLevel' => $lesson->subjectLevel,
                'price' => $lesson->price];
        }

        $userDetail = [
            'id' => $user->id,
            'firstName' => $user->first_name,
            'lastName' => $user->last_name,
            'avatar' => $user->avatar,
            'description' => $user->description,
            'lessons' => $lessonArr,
        ];
        try {
            return response()->json([
                'message' => 'succes',
                'teacher' => $userDetail,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['
                message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(user $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, user $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(user $user)
    {
        //
    }
    public function calendar(Request $request){
        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
            'teacherId' => 'required|integer|exists:users,id'
        ],[
            'date.required' => 'Start date is required',
            'date.date_format' => 'Invalid date format',
            'teacherId.required' => 'Teacher is required',
            'teacherId.integer' => 'Invalid teacher id',
            'teacherId.exists' => 'Invalid teacher id'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'error'  => $validator->errors(),
            ], 400);
        }
        $student = auth()->user();
        if($student->role != "student"){
            return response()->json([
                'message' => 'error',
                'error'  => 'Niepoprawny użytkownk'
            ], 400);
        }
        $studentClasses = classes::join('terms','terms.id','=','classes.terms_id')
            ->where('classes.student_id',$student->id)
            ->whereBetween('terms.start_date',[
                $request->get('date').' 00:00:00',$request->get('date').' 23:59:59'
            ])->get();
        $classesArray = array();
        foreach($studentClasses as $class){
            $classDetails[] = $this->classesDetails($class);
            $classesArray =$classDetails;
        }
        $teacher = user::find($request->get('teacherId'));
        $teacherTerms = term::where('teacher_id',$teacher ->id)
            ->whereBetween('start_date', [$request->get('date') . ' 00:00:00', $request->get('date') . ' 23:59:59'])
            ->get();
        $termArray = array();
        foreach ($teacherTerms as $term) {
            $terms = term::find($term->id)->load('classes');
            if($terms->classes == null)
                $termArray[] = $terms;
        }
        try{
            return response()->json([
                'message' => 'sucess',
                'studentTerms' => $classesArray,
                'teacherTerms' => $termArray,
            ], 200);
        }
        catch(\Exception $e){
            return response()->json(['
                message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }



    }
    private function classesDetails(classes $class ){

        $term = $this->termDetails($class->terms_id);
        $classes = [
            "id"=> $class->id,
            "student"=>$this->userDetails($class->student_id),
            "lesson"=>$this->lessonDetails($class->lesson_id),
            "confirmed"=>$class->confirmed
        ];
        return  [
            "id"=>$term['id'],
            "start_date"=>$term['start_date'],
            "end_date"=>$term['end_date'],
            "teacher"=>$term['teacher'],
            "class"=>$classes];
    }
    private function userDetails(int $user_id){
        $user= user::find($user_id);
        return ["id" => $user->id,
            "first_name"=>$user->first_name,
            "last_name"=>$user->last_name];
    }
    private function termDetails(int $term_id)
    {
        $term = term::find($term_id);
        return ["id"=>$term->id,
            "teacher"=>$this->userDetails($term->teacher_id),
            "start_date"=>$term->start_date,
            "end_date"=>$term->end_date];
    }
    private function lessonDetails(int $lesson_id)
    {
        $lesson = lesson::find($lesson_id);
        return [
            "id"=>$lesson->id,
            "price"=>$lesson->price,
            "subject"=>$this->subjectDetails($lesson->subject_id),
            "subjectLevel"=>$this->subjectLevelDetails($lesson->subject_level_id),
        ];
    }
    private function subjectDetails(int $subject_id)
    {
        return subject::find($subject_id);
    }
    private function subjectLevelDetails(int $subject_level_id){
        return subjectLevel::find($subject_level_id);
    }
}
