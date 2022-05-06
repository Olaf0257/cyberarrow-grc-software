<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Model;

class TasksEvidenceUploadAllowedStatus extends Model
{
    protected $table = 'tasks_evidences_upload_allowed_status';
    protected $fillable = ['project_control_id', 'status'];
}
