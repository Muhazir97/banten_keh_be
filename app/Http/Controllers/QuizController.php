<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\LoggedUser;
use validator;
use App\Quiz;

class QuizController extends Controller
{
// ================================ FOR USER ==============================================

    public function quiz(Request $request)
    {

        $per_page = 1;

        $data = DB::table('t_soal_quiz')
                    // ->where('id', 2)
                    ->paginate($per_page);

        $links = $data->links();

        $dataResult = [
            'data'  => $data,
            'links' => $links,
        ];

        if (count($data) == 0) {
            return Responses::sendError($dataResult, 'Quiz Is Empty');
        }

        return Responses::sendResponse($dataResult, 'Quiz Retrieved Successfully');
    }

// ==================================== FOR ADMIN =============================================

    public function index(Request $request)
    {
        $per_page = 100;

        if(!empty($request->input('search'))){
            $data = DB::table('t_soal_quiz')
                    ->where('soal', 'LIKE', "%".$request->search."%")
                    ->orderBy('created_at', 'DESC')
                    ->paginate($per_page);

            $links = $data->appends(['search' => $request->search])->links();
        } else {
            $data = DB::table('t_soal_quiz')
                    ->orderBy('created_at', 'DESC')
                    ->paginate($per_page);

            $links = $data->links();
        }

        $dataResult = [
            'data'  => $data,
            'links' => $links,
        ];

        if (count($data) == 0) {
            return Responses::sendError($dataResult, 'Quiz Is Empty');
        }

        return Responses::sendResponse($dataResult, 'Quiz Retrieved Successfully');
    }

    public function show($id)
    {
        $data  = Quiz::find($id);

        if (is_null($data)) {
            return Responses::sendError($data, 'Quiz Is Empty');
        }

        return Responses::sendResponse($data, 'Quiz Retrieved Successfully');
    }

    public function store(Request $request)
    {     
        $validator = validator::make($request->all(), [
            'soal'    => 'required',
            'jawaban' => 'required',
            'pil_a'   => 'required',
            'pil_b'   => 'required',
            'pil_c'   => 'required',
            'pil_d'   => 'required',
        ]);

        if($validator->fails()){
        	return Responses::sendError($validator->errors(), 'Validation Error');
        }

        if ($request->hasFile('image')) {
            $attach    = $request->image;
            $original  = $attach->getClientOriginalName();
            $file      = pathinfo($original, PATHINFO_FILENAME);
            $extension = pathinfo($original, PATHINFO_EXTENSION);
            $filename  = $file.'_'.\Carbon\Carbon::now()->format('ymd_his').'.'.$extension;

            $attach->move(storage_path('image'), $filename );
        }

        $data             = new Quiz;
        $data->soal       = $request->input('soal');
        $data->jawaban    = $request->input('jawaban');
        $data->pil_a      = $request->input('pil_a');
        $data->pil_b      = $request->input('pil_b');
        $data->pil_c      = $request->input('pil_c');
        $data->pil_d      = $request->input('pil_d');
        if ($request->hasFile('image')) {
            $data->image  = $filename;
        }
        $data->created_by = LoggedUser::get()['user']->username;      
        $data->save();

        return Responses::sendResponse($data, 'Quiz Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $validator = validator::make($request->all(), [
            'soal'    => 'required',
            'jawaban' => 'required',
            'pil_a'   => 'required',
            'pil_b'   => 'required',
            'pil_c'   => 'required',
            'pil_d'   => 'required',
        ]);

        if($validator->fails()){
            return Responses::sendError($validator->errors(), 'Validation Error');       
        }

        if ($request->hasFile('image')) {
            $attach    = $request->image;
            $original  = $attach->getClientOriginalName();
            $file      = pathinfo($original, PATHINFO_FILENAME);
            $extension = pathinfo($original, PATHINFO_EXTENSION);
            $filename  = $file.'_'.\Carbon\Carbon::now()->format('ymd_his').'.'.$extension;
            
            $attach->move(storage_path('image'), $filename );
        }

        $data          = Quiz::find($id);
        $data->soal    = $request->input('soal');
        $data->jawaban = $request->input('jawaban');
        $data->pil_a   = $request->input('pil_a');
        $data->pil_b   = $request->input('pil_b');
        $data->pil_c   = $request->input('pil_c');
        $data->pil_d   = $request->input('pil_d');
        if ($request->hasFile('image')) {
            $data->image = $filename;
        }
        $data->updated_by = LoggedUser::get()['user']->username;
        $data->save();

        return Responses::sendResponse($data, 'Quiz Updated Successfully');
    }

    public function destroy($id)
    {
        $data = Quiz::destroy($id);

        return Responses::sendResponse($data, 'Quiz Deleted Successfully');
    }
}