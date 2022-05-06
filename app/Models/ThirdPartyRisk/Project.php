<?php

namespace App\Models\ThirdPartyRisk;

use App\Models\DataScope\BaseModel;
use Database\Factories\ThirdPartyRisk\ProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends BaseModel
{
    use HasFactory;

    protected $table = 'third_party_projects';
    protected $appends = ['project_status'];
    protected $fillable = [
        'name',
        'questionnaire_id',
        'launch_date',
        'due_date',
        'timezone',
        'frequency',
        'vendor_id',
        'status'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function getProjectStatusAttribute()
    {
        /*
         * Completed: status is set to archived
         * When status is active, we have 3 possibilities
         * Overdue: We passed due date
         * Not started: We didn't reach launch_date yet
         * In Progress: We're between launch and due date
         * */

        $status = [
            'badge' => 'bg-success',
            'status' => 'Completed'
        ];

        if ($this->status === 'active') {
            if (now()->timezone($this->timezone)->betweenIncluded($this->launch_date, $this->due_date)) {
                $status = [
                    'badge' => 'bg-info',
                    'status' => 'In Progress'
                ];
            } else if (now()->timezone($this->timezone)->lessThan($this->launch_date)) {
                $status = [
                    'badge' => 'bg-danger',
                    'status' => 'Not Started'
                ];
            } else {
                $status = [
                    'badge' => 'bg-warning',
                    'status' => 'Overdue'
                ];
            }
        }

        return $status;

    }

    public function activities()
    {
        return $this->hasMany(ProjectActivity::class, 'project_id', 'id');
    }

    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function email()
    {
        return $this->hasOne(ProjectEmail::class);
    }

    protected static function newFactory()
    {
        return ProjectFactory::new();
    }
}
