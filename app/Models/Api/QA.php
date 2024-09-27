<?php

namespace App\Models\Api;
use Illuminate\Database\Eloquent\Model;


class QA extends Model
{

    protected $table = 'questions';

    protected $fillable = ['question', 'img', 'icon', 'color_code'];


//    static public function QA(){
//        return QA::get();
//    }


}
