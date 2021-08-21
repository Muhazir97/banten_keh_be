<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use App\Kamus;
use validator;
use App\Http\Traits\LoggedUser;

class KamusController extends Controller
{
// ================================ FOR USER ==============================================

    public function index(Request $request)
    {
        $search_input  = $request->search_input;
        $search_output = $request->search_output;
        $keyword       = $request->keyword;

        // Kalimat Input
        if ($search_input == 'indo') {
            $kalimat_input = 'kalimat_indo';
        }else if($search_input == 'jawa'){
            $kalimat_input = 'kalimat_jawa';
        }else if($search_input == 'sunda'){
            $kalimat_input = 'kalimat_sunda';
        }

        // Jenis Pemenggalan
        if ($search_output == 'indo') {
            $pemenggalan   = 'pemenggalan_indo';
        }else if($search_output == 'jawa'){
            $pemenggalan   = 'pemenggalan_jawa';
        }else if($search_output == 'sunda'){
            $pemenggalan   = 'pemenggalan_sunda';
        }

        $master = DB::table('t_bahasa');
                if ($search_output == 'jawa') {
                    $condition = $master->select('jawa as result', $pemenggalan.' as pemenggalan', 'jenis_kata', 'kalimat_jawa as kalimat_output', $kalimat_input.' as kalimat_input', 'image');
                }else if($search_output == 'sunda'){
                    $condition = $master->select('sunda as result', $pemenggalan.' as pemenggalan', 'jenis_kata', 'kalimat_sunda as kalimat_output', $kalimat_input.' as kalimat_input', 'image');
                }else if ($search_output == 'indo') {
                    $condition = $master->select('indo as result', $pemenggalan.' as pemenggalan', 'jenis_kata', 'kalimat_indo as kalimat_output', $kalimat_input.' as kalimat_input', 'image');
                }else{
                    $condition = $master;
                }

                $data = $condition->where($search_input, '=', $keyword)->orderBy('created_at', 'ASC')->get();

        if (count($data) == 0) {
            return Responses::sendError($data, 'Kamus Is Empty');
        }

        return Responses::sendResponse($data, 'Kamus Retrieved Successfully');
    }

// ==================================== FOR ADMIN =============================================

    public function IndexAdmin(Request $request)
    {
        $per_page = 100;

        if(!empty($request->input('search'))){
            $data = DB::table('t_bahasa')
                    ->where('indo', 'LIKE', "%".$request->search."%")
                    ->orWhere('jawa', 'LIKE', "%".$request->search."%")
                    ->orWhere('sunda', 'LIKE', "%".$request->search."%")
                    ->orderBy('created_at', 'DESC')
                    ->paginate($per_page);

            $links = $data->appends(['search' => $request->search])->links();
        } else {
            $data = DB::table('t_bahasa')
                    ->orderBy('created_at', 'DESC')
                    ->paginate($per_page);

            $links = $data->links();
        }

        $dataResult = [
            'data'  => $data,
            'links' => $links,
        ];

        if (count($data) == 0) {
            return Responses::sendError($dataResult, 'Kamus Is Empty');
        }

        return Responses::sendResponse($dataResult, 'Kamus Retrieved Successfully');
    }

    public function show($id)
    {
        $data = Kamus::find($id);

        if (is_null($data)) {
            return Responses::sendError($data, 'Kamus Is Empty');
        }

        return Responses::sendResponse($data, 'Kamus Retrieved Successfully');
    }

    public function store(Request $request)
    {     
        $validator = validator::make($request->all(), [
            'indo'  => 'required',
            'jawa'  => 'required',
            'sunda' => 'required',
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

        $data                    = new Kamus;
        $data->indo              = $request->input('indo');
        $data->jawa              = $request->input('jawa');
        $data->sunda             = $request->input('sunda');   
        $data->jenis_kata        = $request->input('jenis_kata');   
        $data->pemenggalan_indo  = $request->input('pemenggalan_indo');
        $data->pemenggalan_jawa  = $request->input('pemenggalan_jawa');
        $data->pemenggalan_sunda = $request->input('pemenggalan_sunda');
        $data->kalimat_indo      = $request->input('kalimat_indo');   
        $data->kalimat_jawa      = $request->input('kalimat_jawa');   
        $data->kalimat_sunda     = $request->input('kalimat_sunda');   
        if ($request->hasFile('image')) {
            $data->image         = $filename;
        }
        $data->created_by    = LoggedUser::get()['user']->username;      
        $data->save();

        return Responses::sendResponse($data, 'Kamus Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $validator = validator::make($request->all(), [
            'indo'  => 'required',
            'jawa'  => 'required',
            'sunda' => 'required',
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

        $data                    = Kamus::find($id);
        $data->indo              = $request->indo;
        $data->jawa              = $request->jawa;
        $data->sunda             = $request->sunda;
        $data->jenis_kata        = $request->jenis_kata;
        $data->pemenggalan_indo  = $request->pemenggalan_indo;
        $data->pemenggalan_jawa  = $request->pemenggalan_jawa;
        $data->pemenggalan_sunda = $request->pemenggalan_sunda;
        $data->kalimat_indo      = $request->kalimat_indo;
        $data->kalimat_jawa      = $request->kalimat_jawa;
        $data->kalimat_sunda     = $request->kalimat_sunda;
        if ($request->hasFile('image')) {
            $data->image      = $filename;
        }
        $data->updated_by     = LoggedUser::get()['user']->username;
        $data->save();

        return Responses::sendResponse($data, 'Kamus Updated Successfully');
    }

    public function destroy($id)
    {
        $data = Kamus::destroy($id);

        return Responses::sendResponse($data, 'Kamus Deleted Successfully');
    }
}