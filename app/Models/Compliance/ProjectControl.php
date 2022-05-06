<?php

namespace App\Models\Compliance;

use App\Models\DataScope\BaseModel;
use Illuminate\Database\Eloquent\Model;
use \Mews\Purifier\Casts\CleanHtml;

class ProjectControl extends BaseModel
{

    protected $table = 'compliance_project_controls';

    protected $fillable = [
        'applicable',
        'is_editable',
        'current_cycle',
        'name',
        'description',
        'required_evidence',
        'primary_id',
        'id_separator',
        'sub_id',
        'status',
        'amend_status',
        'responsible',
        'approver',
        'deadline',
        'frequency',
        'approved_at',
    ];

    protected $casts = [
        'name' => CleanHtml::class,
        'description' => CleanHtml::class,
        'required_evidence' => CleanHtml::class,
        'primary_id' => CleanHtml::class,
        'sub_id' => CleanHtml::class,
    ];

    protected $appends = ['isEligibleForReview', 'controlId', 'idSeparators'];

    public function project()
    {
        return $this->belongsTo('App\Models\Compliance\Project', 'project_id');
    }

    public function responsibleUser()
    {
        return $this->belongsTo('App\Models\UserManagement\Admin', 'responsible');
    }

    public function approverUser()
    {
        return $this->belongsTo('App\Models\UserManagement\Admin', 'approver');
    }

    public function evidencesUploadStatus()
    {
        return $this->hasOne('App\Models\Tasks\TasksEvidenceUploadAllowedStatus', 'project_control_id');
    }

    public function evidences()
    {
        return $this->hasMany('App\Models\Compliance\Evidence')->orderByDesc('id');
    }

    public function risks()
    {
        return $this->belongsToMany('App\Models\RiskManagement\RiskRegister', 'risks_mapped_compliance_controls', 'control_id', 'risk_id');
    }

    /**
     * get the status whether control is allowed to submit for review.
     */
    public function getIsEligibleForReviewAttribute()
    {
        if (!is_null($this->evidences) && $this->evidences->count() > 0) {
            $evidences = $this->evidences()->get();

            if ($this->current_cycle > 1 && $this->unlocked_at) {
                $evidenceDocsCount = $evidences->where('updated_at', '>', $this->unlocked_at)->count();

                if ($evidenceDocsCount > 0) {
                    // Rejected control case
                    return $this->afterFirst($evidences);
                } else {
                    return false;
                }
            } else if (!in_array($this->amend_status, ["solved", 'submitted', 'requested_responsible', 'rejected']) && $this->amend_status != null) {
                $evidenceDocsUploadedAfterInitialApprovalCount = $evidences->
                where('updated_at', '>', $this->approved_at)
                    ->count();

                if ($evidenceDocsUploadedAfterInitialApprovalCount > 0 && $this->status != "Under Review") {
                    return true;
                }
            } else {
                // after first
                return $this->afterFirst($evidences);
            }
        } else {
            return false;
        }
    }

    public function getControlIdAttribute()
    {
        $controlId = null;

        if (!is_null($this->id_separator)) {
            $separatorId = ($this->id_separator == '&nbsp;') ? ' ' : $this->id_separator;

            $controlId = $this->primary_id . $separatorId . $this->sub_id;
        } else {
            $controlId = $this->primary_id . $this->sub_id;
        }

        return $controlId;
    }

    public function getIdSeparatorsAttribute()
    {
        return [
            '.' => 'Dot Separated',
            '&nbsp;' => 'Space Separated',
            '-' => 'Dash Separated',
            ',' => 'Comma Separated',
        ];
    }

    /**
     * Get the non breaking space if value is space.
     *
     * @return string
     */
    public function getIdSeparatorAttribute($value)
    {
        if ($value == ' ') {
            return '&nbsp;';
        }

        return $value;
    }

    /**
     * Set the id_separator to space if value is non-breaking space.
     *
     * @param string $value
     *
     * @return void
     */
    public function setIdSeparatorAttribute($value)
    {
        if ($value == '&nbsp;') {
            $this->attributes['id_separator'] = ' ';
        } else {
            $this->attributes['id_separator'] = $value;
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $evidences
     * @return bool
     */
    public function afterFirst(\Illuminate\Database\Eloquent\Collection $evidences): bool
    {
        if ($this->status == 'Rejected' && !is_null($this->rejected_at)) {
            $evidenceDocsUploadedAfterRejectionCount = $evidences->where('updated_at', '>', $this->rejected_at)->count();

            return $evidenceDocsUploadedAfterRejectionCount > 0;

        } elseif ($this->status == 'Under Review' || $this->status == 'Implemented') {
            return false;
        } else {
            return true;
        }
    }
}
