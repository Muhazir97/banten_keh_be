<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use DB;
use App\User;

class TestController extends Controller
{
    public function index()
    {
    	$user             = new User;
        $user->username   = 'administrator';
        $user->email      = 'administrator@gmail.com';
        $user->password   = password_hash('123456', PASSWORD_BCRYPT);
        $user->save();
    }
}