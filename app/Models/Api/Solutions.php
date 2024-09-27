<?php

namespace App\Models\Api;
use Illuminate\Database\Eloquent\Model;


class Solutions extends Model
{

    protected $table = 'solutions';

    protected $fillable = ['title', 'problem', 'solution', 'image'];


}
