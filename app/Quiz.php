<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
 
class Quiz extends Model
{
    protected $table    = 't_soal_quiz';
	protected $fillable = ['id','soal','jawaban','pil_a','pil_b','pil_c','pil_d'];
}
