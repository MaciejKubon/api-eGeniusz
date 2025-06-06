<?php

namespace App\Http\Controllers\api;

use App\Models\classes;
use App\Models\term;
use App\Models\user;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class userController extends Controller
{

    public function teacherHomeTerms(){

        if (!auth()->user()->can('show_teacher_home_terms'))
            abort(403);

        $user=auth()->user();
        $dateToday = Carbon::now()->format('Y-m-d H:i');
        $futureDate = Carbon::now()->addDays(7)->format('Y-m-d H:i');
        $terms = term::join('users', 'terms.teacher_id', '=', 'users.id')
            ->leftJoin('classes', 'classes.terms_id', '=', 'terms.id')
            ->leftJoin('users as student', 'classes.student_id', '=', 'student.id')
            ->leftjoin('lessons', 'classes.lesson_id', '=', 'lessons.id')
            ->leftJoin('subjects', 'lessons.subject_id', '=', 'subjects.id')
            ->leftJoin('subject_levels', 'lessons.subject_level_id', '=', 'subject_levels.id')
            ->where('terms.teacher_id', '=', $user->id)
            ->whereBetween('terms.start_date',[$dateToday, $futureDate])
            ->select(
                'terms.id',
                'terms.start_date',
                'terms.end_date',
                'classes.id as class_id',
                'classes.confirmed',
                'student.first_name',
                'student.last_name',
                'subjects.name as subject_name',
                'subject_levels.name as subject_level_name',
                'lessons.price as lesson_price',
            )
            ->get();
        $termArray = array();
        $classesArray = array();
        $confirmClassesArray = array();
        foreach($terms as $term){
            if($term->class_id == null){
                $termArray[] = $term;
            }
            else{
                if($term->confirmed == 1){
                    $confirmClassesArray[] = $term;
                }
                else{
                    $classesArray[] = $term;
                }
            }
        }
        $termsA = [
            'terms' =>$termArray,
            'classes' =>$classesArray,
            'confirmClasses' =>$confirmClassesArray,
        ];
        try {
            return response()->json([
                'message' => 'sucess',
                'term' => $termsA
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Błąd pobieraia danych',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function studentHomeTerms(){
        if (!auth()->user()->can('show_student_home_classes'))
            abort(403);

        $user=auth()->user();
        $dateToday = Carbon::now()->format('Y-m-d H:i');
        $futureDate = Carbon::now()->addDays(7)->format('Y-m-d H:i');
        $terms = classes::join('users', 'classes.student_id', '=', 'users.id')
            ->leftJoin('terms', 'classes.terms_id', '=', 'terms.id')
            ->leftJoin('users as teacher', 'terms.teacher_id', '=', 'teacher.id')
            ->leftjoin('lessons', 'classes.lesson_id', '=', 'lessons.id')
            ->leftJoin('subjects', 'lessons.subject_id', '=', 'subjects.id')
            ->leftJoin('subject_levels', 'lessons.subject_level_id', '=', 'subject_levels.id')
            ->where('classes.student_id', '=', $user->id)
            ->whereBetween('terms.start_date',[$dateToday, $futureDate])
            ->select(
                'terms.id',
                'terms.start_date',
                'terms.end_date',
                'classes.id as class_id',
                'classes.confirmed',
                'teacher.first_name',
                'teacher.last_name',
                'subjects.name as subject_name',
                'subject_levels.name as subject_level_name',
            )
            ->get();
        $termArray = array();
        $classesArray = array();
        $confirmClassesArray = array();
        foreach($terms as $term){
            if($term->class_id == null){
                $termArray[] = $term;
            }
            else{
                if($term->confirmed == 1){
                    $confirmClassesArray[] = $term;
                }
                else{
                    $classesArray[] = $term;
                }
            }
        }
        $termsA = [
            'terms' =>$termArray,
            'classes' =>$classesArray,
            'confirmClasses' =>$confirmClassesArray,
        ];
        try {
            return response()->json([
                'message' => 'Błąd pobieraia danych',
                'term' => $termsA
            ]);
        } catch (\Exception $e) {
            return response()->json(['
                message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }

    }
}
