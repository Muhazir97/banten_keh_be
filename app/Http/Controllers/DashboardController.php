<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\LoggedUser;
use validator;
use App\Content;
use App\Category;
use App\Kamus;

class DashboardController extends Controller
{
    public function ContentTerpopuler()
    {

        $data = Content::orderBy('counter_visit', 'DESC')
                        ->limit(10)
                        ->get();

        if (is_null($data)) {
            return Responses::sendError($data, 'Content Terpopuler Is Empty');
        }

        return Responses::sendResponse($data, 'Content Terpopuler Retrieved Successfully');
    }

    public function CardKamus()
    {
        $total_data       = Kamus::select('id')->count();
        $last_update_data = Kamus::orderBy('created_at', 'DESC')->pluck('created_at')->first();

        $dataResult = [
            'total_data'       => $total_data,
            'last_update_data' => $last_update_data,
        ];

        return Responses::sendResponse($dataResult, 'Card Kamus Retrieved Successfully');
    }

    public function CardContent()
    {
        $total_data       = Content::select('id')->count();
        $last_update_data = Content::orderBy('created_at', 'DESC')->pluck('created_at')->first();

        $dataResult = [
            'total_data'       => $total_data,
            'last_update_data' => $last_update_data,
        ];

        return Responses::sendResponse($dataResult, 'Crd Content Retrieved Successfully');
    }
}