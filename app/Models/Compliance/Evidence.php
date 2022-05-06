<?php

namespace App\Models\Compliance;

use Database\Factories\EvidenceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Mews\Purifier\Casts\CleanHtml;

class Evidence extends Model
{
    use HasFactory;

    protected $table = 'compliance_project_control_evidences';
    protected $fillable = ['project_control_id', 'name', 'path', 'type', 'text_evidence', 'status', 'deadline'];


    protected $casts = [
        'name' => CleanHtml::class,
        'name2' => CleanHtml::class,
        'text_evidence' => CleanHtml::class,
        'text_evidence_name' => CleanHtml::class
    ];


    public function justifications()
    {
        return $this->hasMany('App\Models\Compliance\Justification');
    }

    public function projectControl()
    {
        return $this->belongsTo('App\Models\Compliance\ProjectControl', 'project_control_id', 'id');
    }


    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return EvidenceFactory::new();
    }
}
