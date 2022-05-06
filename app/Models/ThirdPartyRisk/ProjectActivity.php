<?php

namespace App\Models\ThirdPartyRisk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectActivity extends Model
{
    use HasFactory;

    protected $table = 'third_party_project_activities';
    protected $fillable = ['project_id','activity', 'type'];


}
