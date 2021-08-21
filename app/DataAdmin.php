<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
 
class DataAdmin extends Model
{
    protected $table    = 'users';
	protected $fillable = ['id','username','password','email','image','role','created_at','updated_at'];
}
