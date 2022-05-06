<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Model;

class TasksSubmitToApproverAllowedStatus extends Model
{
    protected $table = 'tasks_submit_to_approver_allowed_status'; 
    protected $fillable = ['project_control_id', 'status'];
}
