<?php

namespace App\Models\ThirdPartyRisk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionAnswer extends Model
{
    use HasFactory;

    protected $table = 'third_party_question_answers';
    protected $fillable = ['project_id' , 'question_id', 'answer', 'created_at', 'updated_at'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

}
