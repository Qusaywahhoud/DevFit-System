<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompletedExercise extends Model
{
    protected $fillable = ['user_id', 'exercise_title'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}