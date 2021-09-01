<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
 
class Category extends Model
{
    protected $table    = 't_category';
	protected $fillable = ['id','content_id','category_content','created_by'];
}
