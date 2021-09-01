<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use App\DataAdmin;
use validator;
use App\Http\Traits\LoggedUser;

class DataAdminController extends Controller
{
    public function index(Request $request)
    {
        $per_page = 100;

        if(!empty($request->input('search'))){
            $data = DB::table('users')
                    ->where('username', 'LIKE', "%".$request->search."%")
                    ->orderBy('created_at', 'DESC')
                    ->paginate($per_page);

            $links = $data->appends(['search' => $request->search])->links();
        } else {
            $master = DB::table('users');
                    if (LoggedUser::get()['user']->role != 'Admin') {
                        $condition = $master->where('username', LoggedUser::get()['user']->username);
                    }else{
                        $condition = $master;
                    }

            $data = $condition->orderBy('created_at', 'DESC')->paginate($per_page);

            $links = $data->links();
        }

        $role = LoggedUser::get()['user']->role;

        $dataResult = [
            'data'  => $data,
            'links' => $links,
            'role'  => $role,
        ];

        if (count($data) == 0) {
            return Responses::sendError($dataResult, 'Kamus Is Empty');
        }

        return Responses::sendResponse($dataResult, 'Kamus Retrieved Successfully');
    }

    public function show($id)
    {
        $data = DataAdmin::find($id);

        if (is_null($data)) {
            return Responses::sendError($data, 'Account Is Empty');
        }

        return Responses::sendResponse($data, 'Account Retrieved Successfully');
    }

    public function store(Request $request)
    {     
        $validator = validator::make($request->all(), [
            'username' => 'required',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required',
            'role'     => 'required',
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

        $data           = new DataAdmin;
        $data->username = $request->input('username');
        $data->email    = $request->input('email');
        $data->password = password_hash($request->input('password'), PASSWORD_BCRYPT);   
        $data->role     = $request->input('role');    
        if ($request->hasFile('image')) {
            $data->image = $filename;
        }
        $data->save();

        return Responses::sendResponse($data, 'Account Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $validator = validator::make($request->all(), [
            'username' => 'required',
            'email'    => 'required|email|unique:users,email,'.$id,
            'role'     => 'required',
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

        $data           = DataAdmin::find($id);
        $data->username = $request->username;
        $data->email    = $request->email;
        $data->password = $request->password;
        $data->role     = $request->role;
        if ($request->hasFile('image')) {
            $data->image = $filename;
        }
        $data->save();

        return Responses::sendResponse($data, 'Account Updated Successfully');
    }

    public function destroy($id)
    {
        $data = DataAdmin::destroy($id);

        return Responses::sendResponse($data, 'Account Deleted Successfully');
    }
}