<?php

namespace App\Http\Controllers\api;

use App\Models\lesson;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class lessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->can('show_admin_lesson'))
            abort(403);

        $lessons = lesson::all();
        $lessonArray = array();
        foreach($lessons as $lesson){
            $lessonArray[] = $this->lesssonDetails($lesson);
        }
        try{
            return response()->json([
                'message' => 'sucess',
                'lesson' => $lessonArray,
            ], 200);
        }
        catch(\Exception $e){
            return response()->json(['
                message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function showLessons(Request $request){
        if (!auth()->user()->can('show_teacher_lessons'))
            abort(403);
        $user = auth()->user();
        $lessons = $user->lesson;
        $lessonArray = array();
        foreach($lessons as $lesson){
            $lessonArray[] = $this->lesssonDetails($lesson);
        }
        try{
            return response()->json([
                'message' => 'sucess',
                'lessons' => $lessonArray
            ]);}
        catch(\Exception $e){
            return response()->json(['
                message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function showTeacherLessons(User $user){
        if (!auth()->user()->can('show_all_teacher_lesson'))
            abort(403);

        $lessons = $user->lesson;
        $lessonArray = array();
        foreach($lessons as $lesson){
            $lessonArray[] = $this->lesssonDetails($lesson);
        }
        try{
        return response()->json([
            'message' => 'sucess',
            'lessons' => $lessonArray
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
        if (!auth()->user()->can('create_lesson'))
            abort(403);
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required',
            'subject_level_id' => 'required',
            'price' => 'required|min:0',
        ],[
            'subject_id.required' => 'Subject is required',
            'subject_level_id.required' => 'Subject level is required',
            'price.required' => 'Price is required',
            'price.min' => 'Price must be at least 0',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Nie udało sie utworzenie przedmiotu',
                'error'  => $validator->errors(),
            ], 400);
        }
        $users = $request->user();
        $user = User::find($users->id);

        $lessonList =$users->lesson;
        if($lessonList->where('subject_id',$request['subject_id'])->where('subject_level_id',$request['subject_level_id'])->count() > 0){
            return response()->json([
                'message' => 'Nie udało sie utworzenie przedmiotu',
                'error'  => "Istnieje już lekacja z wybranego przedmiotu o wybranym poziomie"
            ], 400);
        }
        try{
            lesson::create([
                'teacher_id' => $user->id,
                'subject_id' => $request['subject_id'],
                'subject_level_id' => $request['subject_level_id'],
                'price' => $request['price']
            ]);
            return response()->json([
                'message' => 'Przedmiot został dodany',
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
    public function show(lesson $lesson)
    {
        try {
            return response()->json([
                'message' => 'sucess',
                'lesson' => $this->lesssonDetails($lesson)
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
    public function update(Request $request, lesson $lesson)
    {
        if (!auth()->user()->can('edit_lesson'))
            abort(403);
        $users = $request->user();
        $user = User::find($users->id);
        if($user->role == 'teacher' && $lesson->user_id != $user->id){
            abort(403);
        }
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required',
            'subject_level_id' => 'required',
            'price' => 'required|min:0',
        ],[
            'subject_id.required' => 'Subject is required',
            'subject_level_id.required' => 'Subject level is required',
            'price.required' => 'Price is required',
            'price.min' => 'Price must be at least 0',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'error'  => $validator->errors(),
            ], 400);
        }

        $lessonList =$users->lesson;
        if($lessonList->where('subject_id',$request['subject_id'])->
            where('subject_level_id',$request['subject_level_id'])->
            where('teacher_id','!=',$users->id)->
            count() > 0){
            return response()->json([
                'message' => 'error',
                'error'  => "Istnieje już lekacja z wybranego przedmiotu o wybranym poziomie"
            ], 400);
        }
        try {
            $lesson->update($request->all());
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
    public function destroy(lesson $lesson)
    {
        $teacher = auth()->user();
        if (!auth()->user()->can('delete_lesson') | $teacher->id !=$lesson->teacher_id )
            abort(403);
        $class = $lesson->classes;

        if(count($class)>0){
            return response()->json([
                'message' => 'Nie można usunąć przedmiotu przypisanego do zajęć' ,
            ], 400);
        }


        try{
            $lesson->delete();
            return response()->json([
                'message' => 'Usunięcie przedmiotu powiodło się',
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'Nie udało się usunąć przdmiotu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function lesssonDetails(lesson $lesson)
    {
        $lesson = $lesson->load('subject','subjectLevel','user');
        $subject = ['id'=>$lesson->subject->id,
            'name'=>$lesson->subject->name];
        $subject_level = ['id'=>$lesson->subjectLevel->id,
            'name'=>$lesson->subjectLevel->name];
        $teacher = ['id' => $lesson->user->id,
            'firstName'=> $lesson->user->firstName,
            'lastName'=>$lesson->user->lastName];
        try {
            $lesson = [
                'id' => $lesson['id'],
                'subject' => $subject,
                'subject_level' => $subject_level,
                'teacher' => $teacher,
                'price'=>$lesson->price,
            ];
            return $lesson;
        }catch(\Exception $e){
            return response()->json([
                'message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }




}
