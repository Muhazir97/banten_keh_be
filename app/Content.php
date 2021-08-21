<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
 
class Content extends Model
{
	// public $timestamps  = false;
    protected $table    = 't_contents';
	protected $fillable = ['id','judul_content','description_content','image_content','asset_content','counter_visit','created_by','created_at', 'deskripsi_singkat'];

    public function ContentCategory() 
    {
    	return $this->hasMany('App\Category', 'content_id', 'id');
    }
}
