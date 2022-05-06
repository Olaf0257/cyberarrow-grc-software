<?php

namespace App\Models\Compliance;

use Illuminate\Database\Eloquent\Model;
use \Mews\Purifier\Casts\CleanHtml;

class Justification extends Model
{

    protected $table = 'compliance_project_control_justifications';
    protected $fillable = ['project_control_id', 'justification', 'for', 'creator_id'];


    protected $casts = [
        'justification'    => CleanHtml::class,
    ];


    public function creator() {
        return $this->belongsTo('App\Models\UserManagement\Admin');
    }

    public function getJustificationAttribute($value){
        $bbcode = new \Golonka\BBCode\BBCodeParser;
        return $bbcode->parse($value);
    }

}
