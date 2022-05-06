<?php

namespace App\Models\ThirdPartyRisk;

use App\Models\DataScope\BaseModel;
use Database\Factories\ThirdPartyRisk\QuestionnaireFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Casts\CleanHtml;

class Questionnaire extends BaseModel
{
    use HasFactory;

    protected $table = 'third_party_questionnaires';
    protected $fillable = ['name', 'version', 'is_default'];

    protected $casts = [
        'name'    => CleanHtml::class,
        'version'    => CleanHtml::class,
    ];

    public function questions()
    {
        return $this->hasMany(Question::class, 'questionnaire_id');
    }

    protected static function newFactory()
    {
        return QuestionnaireFactory::new();
    }
}
