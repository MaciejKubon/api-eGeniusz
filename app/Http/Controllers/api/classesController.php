<?php

namespace App\Http\Controllers\api;

use App\Models\classes;
use App\Http\Controllers\Controller;
use App\Models\lesson;
use App\Models\subject;
use App\Models\subjectLevel;
use App\Models\term;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Monolog\Level;

class classesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->can('show_admin_classes'))
            abort(403);
        $classes = classes::all();
        $classesArr = array();
        foreach ($classes as $class) {
            $classesArr[] = $this->classesDetails($class);
        }
        try{
            return response()->json([
                'message' => 'sucess',
                'classes' => $classesArr,
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
        if (!auth()->user()->can('create_classes')){
            abort(403);
        }
        $validator = Validator::make($request->all(), [
            'terms_id' => 'required|exists:terms,id',
            'lesson_id' => 'required|exists:lessons,id',
            'confirmed' => 'required|boolean',
        ],[
            'terms_id.required' => 'Term is required',
            'terms_id.exists' => 'Term is not exists',
            'lesson_id.required' => 'Przedmiot jest wymagany',
            'lesson_id.exists' => 'Wybrany przedmiot nie istniej',
            'confirmed.required' => 'Confirmed is required',
            'confirmed.boolean' => 'Confirmed is not valid'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Błąd rezerwacji lekcji',
                'error'  => $validator->errors(),
            ], 400);
        }
        $student = User::find(auth()->user()->id);
        $term = term::find($request->terms_id);
        $termStart = $term->start_date;
        $termEnd = $term->end_date;
        $studentClasses = $student->classes;
        foreach ($studentClasses as $class) {
            $terms = $class->terms;
            if((($termStart>=$terms->start_date) && ($termEnd<=$terms->end_date)) ||
                (($termStart<=$terms->start_date) && ($termEnd>=$terms->end_date))||
                (($termStart>=$terms->start_date) && ($termEnd<=$terms->end_date)) ||
                (($termStart<=$terms->start_date) && ($termEnd>=$terms->end_date))
            ){
                return response()->json([
                    'message' => 'Data zajęć koliduje z innymi zajęciami',
                    'error' => "Data zajęć koliduje z innymi zajęciami"
                ], 400);
            }
        }
        try {
            classes::create([
                'student_id' => $student -> id,
                "terms_id"=>$request-> terms_id,
                "lesson_id"=> $request ->lesson_id,
                "confirmed"=> 0
            ]);
            return response()->json([
                'message' => 'Lekcja  została zarezerwowana',
            ], 201);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Błąd rezerwacji lekcji',
                'error' => $e->getMessage()
            ], 500);
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(classes $classes)
    {
        try {
            return response()->json([
                'message' => 'sucess',
                'terms' => $this->classesDetails($classes)
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, classes $classes)
    {
        if (!auth()->user()->can('update_classes')){
            abort(403);
        }
        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required|exists:lessons,id',
        ],[
            'lesson_id.required' => 'Lesson is required',
            'lesson_id.exists' => 'Lesson is not exists',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'error'  => $validator->errors(),
            ], 400);
        }
        if($classes->confirmed==true){
            return response()->json([
                'message' => 'error',
                'error'  => "Zajęcia zostały już potwierdzono, nie można ich edytować",
            ], 400);
        }
        try {
            classes::update([
                "lesson_id"=> $request ->lesson_id
            ]);
            return response()->json([
                'message' => 'sucess',
            ], 201);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }

    }
    public function confirm(Request $request)
    {
        if (!auth()->user()->can('confirm_classes')){
            abort(403);
        }
        $validator = Validator::make($request->all(), [
            'confirmed' => 'required|boolean',
            'terms_id' => 'required|exists:terms,id',
        ],[
            'confirmed.required' => 'confirmed is required',
            'confirmed.boolean' => 'Confirmed is not valid',
            'terms_id.required' => 'Term is required',
            'terms_id.exists' => 'Term is not exists',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Nie udało się potwierdzić lekcji',
                'error'  => $validator->errors(),
            ], 400);
        }
        $term = term::find($request->terms_id);
        $teacher = auth()->user();

        if($term->teacher_id!=$teacher->id){
            return response()->json([
                'message' => 'Bład aktualizacji zajęć',
                'error' => 'Incorrect user'
            ], 400);
        }
        $term = $term->load('classes');

        if($term->classes==null){
            return response()->json([
                'message' => 'Bład aktualizacji zajęć',
                'error' => 'Incorrect class id'
            ], 400);
        }
        $classes = classes::find($term->classes->id);

        try {
            $classes->update([
                "confirmed"=> $request->confirmed
            ]);
            return response()->json([
                'message' => 'Zajęcia zostały potwiedzone',
            ], 201);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Błąd aktualizacji zajęć',
                'error' => $e->getMessage()
            ], 500);
        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(classes $classes)
    {
        if (!auth()->user()->can('delete_classes')){
            abort(403);
        }
        $auth = auth()->user();
        if($auth->role == "student" && $auth->id != $classes->student_id){
            abort(403);
        }
        $term = $classes->load('terms');
        if($auth->role == "teacher" && $auth->id != $term->terms->teacher_id){
            abort(403);
        }
        if($classes->condirmded == 1){
            return response()->json([
                'message' => 'Nie można odwołać potwierdzonej lekcji',
                'error' => 'Nie można odwołać potwierdzonej lekcji'
            ], 400);
        }
        try {
            $classes->delete();
            return response()->json([
                'message' => 'Lekcja została anulowana',
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Anulowanie lekcji powiodło się',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function showDayStudentClasses(Request $request){

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
        ],[
            'date.required' => 'Start date is required',
            'date.date_format' => 'Invalid date format',
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
        try{
            return response()->json([
                'message' => 'sucess',
                'terms' => $classesArray,
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
