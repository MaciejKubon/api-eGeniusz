<?php

namespace App\Http\Controllers\api;

use App\Models\lesson;
use App\Models\subjectLevel;
use App\Models\user;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class teacherListController extends Controller
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
        //
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
    public function teacherList(Request $request){

        $validator = Validator::make($request->all(),[
            'subjects_id' => 'array',
            'levels_id' => 'array',
            'minPrice' => 'nullable|numeric|min:0',
            'maxPrice' => 'nullable|numeric|gte:minPrice',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Nieprawidłowe dane filtorwania',
                'error' => $validator->errors(),
            ], 400);
        }
        $filtrSubject = $request['subjects_id'];
        $filerLevel = $request['levels_id'];
        $filterPriceMin = $request['minPrice'];
        $filterPriceMax = $request['maxPrice'];


        $teachers = DB::table('users as t')
            ->join('lessons as l', 'l.teacher_id', '=', 't.id')
            ->when(!empty($filtrSubject), function ($query) use ($filtrSubject) {
                $query->whereIn('l.subject_id', $filtrSubject);
            })
            ->when(!empty($filerLevel), function ($query) use ($filerLevel) {
                $query->whereIn('l.subject_level_id', $filerLevel);
            })
            ->when(isset($filterPriceMin, $filterPriceMax), function ($query) use ($filterPriceMin, $filterPriceMax) {
                $query->whereBetween('l.price', [$filterPriceMin, $filterPriceMax]);
            })
            ->select('t.id')
            ->distinct()
            ->get();

        $teacherList = array();
        foreach ($teachers as $teacher) {
            $user = User::find($teacher->id);
            $lessons = $user->lesson;
            $subjetArr = array();
            $subjetLevelArr = array();
            $priceArr = array();
            foreach ($lessons as $lesson) {
                $less = Lesson::find($lesson->id)->load('subject', 'subjectLevel');
                $subjetArr[] = $less->subject->name;
                $subjetLevelArr[] = $less->subjectLevel->name;
                $priceArr[] = $less->price;
            }
            sort($priceArr);
            $teacherList[] = [
                'id' => $user->id,
                'firstName' => $user->first_name,
                'lastName' => $user->last_name,
                'avatar' => $user->avatar,
                'subjects' => $subjetArr,
                'subjectLevels' => $subjetLevelArr,
                'price' => $priceArr
            ];
        }
        try {
            return response()->json([
                'message' => 'succes',
                'teachers' => $teacherList,
            ],200);
        }
        catch(\Exception $e){
            return response()->json(['
                message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
