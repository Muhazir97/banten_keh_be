<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\LoggedUser;
use validator;
use App\Content;
use App\Category;

class ContentController extends Controller
{
// ================================ FOR USER ==============================================

    public function branda(Request $request)
    {

        $limit = 5 + $request->plus_page;

        $data = Content::with('ContentCategory')
                ->orderBy('created_at', 'DESC')
                ->limit($limit)
                ->get();

        if (count($data) == 0) {
            return Responses::sendError($data, 'Content Branda Is Empty');
        }

        return Responses::sendResponse($data, 'Content Branda Retrieved Successfully');
    }

    public function searchByCatagory(Request $request)
    {
        $search = $request->category;
        $data = Content::with('ContentCategory')
                ->whereHas('ContentCategory', function($q) use ($search){
                   $q->where('category_content', $search);
                })
                ->orderBy('created_at', 'DESC')
                ->limit(10)
                ->get();

        if (count($data) == 0) {
            return Responses::sendError($data, 'Search By Category Is Empty');
        }

        return Responses::sendResponse($data, 'Search By Category Retrieved Successfully');
    }

    public function detailContent($id)
    {
        $data = Content::find($id);

        $content_terpopuler = Content::select('id', 'judul_content','counter_visit')
                                    ->orderBy('counter_visit', 'DESC')
                                    ->limit(10)
                                    ->get();
        $dataResult = [
            'data'               => $data,
            'content_terpopuler' => $content_terpopuler,
        ];

        if (is_null($data)) {
            return Responses::sendError($dataResult, 'Detail Content Is Empty');
        }

        return Responses::sendResponse($dataResult, 'Detail Content Retrieved Successfully');
    }

    public function CounterVisit($id)
    {
        $get   = Content::where('id', $id)->pluck('counter_visit')->first();
        $total = $get + 1 ;

        $data = Content::find($id)
                ->update([
                    'counter_visit' => $total,
                ]);

        return Responses::sendResponse($total, 'Counter Visit Successfully');
    }

// ==================================== FOR ADMIN =============================================

    public function IndexContentAdmin(Request $request)
    {
        $per_page = 100;

        if(!empty($request->input('search'))){
            $data = DB::table('t_contents')
                    ->where('judul_content', 'LIKE', "%".$request->search."%")
                    ->orderBy('created_at', 'DESC')
                    ->paginate($per_page);

            $links = $data->appends(['search' => $request->search])->links();
        } else {
            $data = DB::table('t_contents')
                    ->orderBy('created_at', 'DESC')
                    ->paginate($per_page);

            $links = $data->links();
        }

        $dataResult = [
            'data'  => $data,
            'links' => $links,
        ];

        if (count($data) == 0) {
            return Responses::sendError($dataResult, 'Content Is Empty');
        }

        return Responses::sendResponse($dataResult, 'Content Retrieved Successfully');
    }

    public function show($id)
    {
        $content  = Content::find($id);
        $category = Category::where('content_id', $id)->get();

        $dataResult = [
            'content'  => $content,
            'category' => $category,
        ];

        if (is_null($content)) {
            return Responses::sendError($content, 'Content Is Empty');
        }

        return Responses::sendResponse($dataResult, 'Content Retrieved Successfully');
    }

    public function store(Request $request)
    {     
        $validator = validator::make($request->all(), [
            'judul_content'       => 'required',
            'description_content' => 'required',
        ]);

        if($validator->fails()){
        	return Responses::sendError($validator->errors(), 'Validation Error');
        }

        $attach    = $request->image_content;
        $original  = $attach->getClientOriginalName();
        $file      = pathinfo($original, PATHINFO_FILENAME);
        $extension = pathinfo($original, PATHINFO_EXTENSION);
        $filename  = $file.'_'.\Carbon\Carbon::now()->format('ymd_his').'.'.$extension;

        $attach->move(storage_path('image_content'), $filename );

        $data                      = new Content;
        $data->judul_content       = $request->input('judul_content');
        $data->deskripsi_singkat   = $request->input('deskripsi_singkat');
        $data->description_content = $request->input('description_content');
        $data->image_content       = $filename;
        $data->asset_content       = $request->input('asset_content');   
        $data->created_by          = LoggedUser::get()['user']->username;      
        $data->save();

        for ($i=0; $i < count($request->input('category_content')); $i++) { 
            $category_content = $request->input('category_content')[$i];

            Category::create([
                'content_id'       => $data->id,
                'category_content' => $category_content, 
                'created_by'       => LoggedUser::get()['user']->username,
            ]);
        }

        return Responses::sendResponse($data, 'Content Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $validator = validator::make($request->all(), [
            'judul_content'       => 'required',
            'description_content' => 'required',
        ]);

        if($validator->fails()){
            return Responses::sendError($validator->errors(), 'Validation Error');       
        }

        if ($request->hasFile('image_content')) {
            $attach    = $request->image_content;
            $original  = $attach->getClientOriginalName();
            $file      = pathinfo($original, PATHINFO_FILENAME);
            $extension = pathinfo($original, PATHINFO_EXTENSION);
            $filename  = $file.'_'.\Carbon\Carbon::now()->format('ymd_his').'.'.$extension;
            
            $attach->move(storage_path('image_content'), $filename );
        }

        $data                      = Content::find($id);
        $data->judul_content       = $request->judul_content;
        $data->deskripsi_singkat   = $request->deskripsi_singkat;
        $data->description_content = $request->description_content;
        if ($request->hasFile('image_content')) {
            $data->image_content = $filename;
        }
        $data->updated_by     = LoggedUser::get()['user']->username;
        $data->save();

        if (!empty($request->input('category_content'))) {
            Category::where('content_id', $id)->delete();
            for ($i=0; $i < count($request->input('category_content')); $i++) { 
                $category_content = $request->input('category_content')[$i];

                Category::create([
                    'content_id'       => $id,
                    'category_content' => $category_content,
                    'created_by'       => LoggedUser::get()['user']->username,
                ]);
            }
        }

        return Responses::sendResponse($data, 'Content Updated Successfully');
    }

    public function destroy($id)
    {
        $data = Content::destroy($id);

        return Responses::sendResponse($data, 'Content Deleted Successfully');
    }

    public function uploadImageCk(Request $request)
    {     

        $attach    = $request->file;
        $original  = $attach->getClientOriginalName();
        $file      = pathinfo($original, PATHINFO_FILENAME);
        $extension = pathinfo($original, PATHINFO_EXTENSION);
        $filename  = $file.'_'.\Carbon\Carbon::now()->format('ymd_his').'.'.$extension;

        $attach->move(storage_path('file'), $filename );

        return Responses::sendResponse('http://localhost/banten_keh_be_lumen/storage/file/'.$filename, 'Content Updated Successfully');
    }
}