<?php

namespace App\Models\ThirdPartyRisk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectEmail extends Model
{
    use HasFactory;

    protected $table = 'third_party_project_emails';
    protected $fillable = ['project_id', 'token'];

    public function project () {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
