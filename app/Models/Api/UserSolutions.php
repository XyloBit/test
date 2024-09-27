<?php

namespace App\Models\Api;
use Illuminate\Database\Eloquent\Model;


class UserSolutions extends Model
{

    protected $table = 'user_solutions';

    protected $fillable = ['uid', 'solution_id', 'select_questions', 'solution_status'];




}
