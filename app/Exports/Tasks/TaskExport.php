<?php

namespace App\Exports\Tasks;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Compliance\ProjectControl;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TaskExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $exportData = [];
        $request = request();
        // dd($request->all());

        $selectedDepartments = $request->selected_departments ?: [];

        $tasksQuery = ProjectControl::leftJoin('compliance_projects', 'compliance_project_controls.project_id', '=', 'compliance_projects.id')
            ->leftJoin('admins as approver', 'compliance_project_controls.approver', '=', 'approver.id')
                ->leftJoin('admins as responsible', 'responsible', '=', 'responsible.id')
                ->leftJoin('admin_departments as approver_departments', 'approver.id', '=', 'approver_departments.admin_id')
                ->leftJoin('admin_departments as responsible_departments', 'responsible.id', '=', 'responsible_departments.admin_id')
                    ->where(function ($q) use ($request) {
                        if ($request->has('selected_projects')) {
                            $q->whereIn('compliance_projects.id', explode(',', $request->selected_projects));
                        }

                        if ($request->project_name) {
                            $q->where('compliance_projects.name', 'LIKE', '%'.$request->project_name.'%');
                        }

                        if ($request->standard_name) {
                            $q->where('compliance_projects.standard', 'LIKE', '%'.$request->standard_name.'%');
                        }

                        if ($request->control_name) {
                            $q->where('compliance_project_controls.name', 'LIKE', '%'.$request->control_name.'%');
                        }

                        if ($request->status) {
                            // handling filters

                            if ($request->status == 'active') {
                                $q->where('deadline', '>', date('Y-m-d'));
                                $q->where('compliance_project_controls.status', '!=', 'Implemented');
                            }

                            if ($request->status == 'due_today') {
                                $q->where('deadline', date('Y-m-d'));
                                $q->where('compliance_project_controls.status', '!=', 'Implemented');
                            }

                            if ($request->status == 'pass_due') {
                                $q->where('deadline', '<', date('Y-m-d'));
                                $q->where('compliance_project_controls.status', '!=', 'Implemented');
                            }

                            if ($request->status == 'under_review') {
                                $q->where('compliance_project_controls.status', 'Under Review');
                            }

                            if ($request->status == 'need-my-approval') {
                                $q->where('compliance_project_controls.status', 'Under Review');
                            }
                        }

                        // handling filters
                        if ($request->responsible_user) {
                            $q->where('responsible', $request->responsible_user);
                        }

                        if ($request->approver_user) {
                            $q->where('approver', $request->approver_user);
                        }

                        if ($request->due_date) {
                            $q->where('deadline', $request->due_date);
                        }

                        if ($request->completion_date) {
                            $q->where('approved_at', $request->completion_date);
                        }

                        if ($request->approval_status) {
                            $q->where('compliance_project_controls.status', $request->approval_status);
                        }
                    })->when($selectedDepartments, function ($query) use ($selectedDepartments) {
                        if (in_array(0, $selectedDepartments)) {
                            $query->orWhereIn('approver_departments.department_id', $selectedDepartments )->orWhereNull('approver_departments.department_id');
                            $query->orWhereIn('responsible_departments.department_id', $selectedDepartments )->orWhereNull('responsible_departments.department_id');
                        } else {
                            $query->orWhereIn('approver_departments.department_id', $selectedDepartments );
                            $query->orWhereIn('responsible_departments.department_id', $selectedDepartments );
                        }
                    });

        $tasks = $tasksQuery
                                ->select([
                                    'deadline',
                                    'compliance_project_controls.status',
                                    'compliance_projects.standard as standard_name',
                                    'approved_at',
                                    'compliance_project_controls.id as id',
                                    'compliance_project_controls.name as control',
                                    'compliance_project_controls.description as control_description',
                                    'compliance_projects.id as project_id',
                                    'compliance_projects.name as project_name',
                                    'responsible.first_name as responsible_first_name',
                                    'responsible.last_name as responsible_last_name',
                                    'approver.first_name as approver_first_name',
                                    'approver.last_name as approver_last_name',
                                ])
                                    ->get();

                                    // dd($tasks);

        foreach($tasks as $task){
            $satifiedData = '';
            $dueDate = '';
            $module = 'Compliance';
            $status = '';
            $today = date('Y-m-d');

            if ($task->status == 'Implemented') {
                $satifiedData = date('jS F, Y', strtotime($task->approved_at));
            } else {
                $dueDate = date('jS F, Y', strtotime($task->deadline));
            }

            //Active
            if (($task->deadline > date('Y-m-d')) && $task->status != 'Implemented') {
                $status = 'Upcoming';
            }

            // Pass due
            if (($task->deadline < date('Y-m-d')) && $task->status != 'Implemented') {
                $status = 'Past Due';
            }

            // Due today
            if (($task->deadline == date('Y-m-d')) && $task->status != 'Implemented') {
                $status = 'Due Today';
            }

            $exportData[] = [
                $task->project_name,
                $task->standard_name,
                $task->control,
                $task->control_description,
                $module,
                $task->responsible_first_name.' '.$task->responsible_last_name,
                $task->approver_first_name.' '.$task->approver_last_name,
                $satifiedData,
                $dueDate,
                $status,
                $task->status
            ];
        }



        return new Collection(
            $exportData
        );
    }

    public function headings(): array {
        $headingArray = [
            'Project',
            'Standard',
            'Control',
            'Control Description',
            'Type',
            'Assigned',
            'Approver',
            'Completion date',
            'Due Date',
            'Status',
            'Approval Stage'
        ];

        return $headingArray;
    }
}
