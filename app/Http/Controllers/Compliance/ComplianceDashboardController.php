<?php

namespace App\Http\Controllers\Compliance;

use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Compliance\ProjectControl;
use App\Models\GlobalSettings\GlobalSetting;
use Illuminate\Support\Facades\Log;

class ComplianceDashboardController extends Controller
{
    protected $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware(function ($request, $next) {
            $this->loggedUser = Auth::guard('admin')->user();

            return $next($request);
        });
    }

    public function dashboard()
    {
      
        // Check If The Application Is Updated Or Not if there is update key  in session
        if(session()->get('updated'))
        {
          
            $shellCmd = 'cd ' . base_path() . ' && php artisan up && php artisan optimize:clear';
            shell_exec($shellCmd);
            // app()->make(\App\LicenseBox\Composer::class)->run(['dump-autoload']);
            session()->forget('updated');
        }

        \View::share('page_title', 'My Dashboard');

        $loggedInUserData = Auth::guard('admin')->user();
        $authUser = $loggedInUserData->only(['id', 'avatar', 'first_name', 'last_name', 'full_name']);
        $authUserRoles = $loggedInUserData->roles()->pluck('name');
        $globalSetting = GlobalSetting::first();

        return Inertia::render('compliance/dashboard/Dashboard', compact('authUser', 'authUserRoles', 'globalSetting'));
    }

    public function getDashboardData(Request $request)
    {
        $data = $this->loadDashboardData();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    private function loadDashboardData()
    {
        $totalUnderReviewMyTasks = ProjectControl::withoutGlobalScopes()->where('applicable', 1)
            ->where('responsible', $this->loggedUser->id)
            ->where('status', 'Under Review')
            ->count();
        $totalNeedMyApprovalTasks = ProjectControl::withoutGlobalScopes()->where('applicable', 1)
            ->where('approver', $this->loggedUser->id)
            ->where('status', 'Under Review')
            ->count();
        $totalTaskDueToday = ProjectControl::withoutGlobalScopes()->where('applicable', 1)
            ->where('deadline', date('Y-m-d'))
            ->where('status', '!=', 'Implemented')
            ->where('responsible', $this->loggedUser->id)
            ->count();
        $totalMyTaskPassDue = ProjectControl::withoutGlobalScopes()->whereDate('deadline', '<', date('Y-m-d'))
            ->where('status', '!=', 'Implemented')
            ->where('responsible', $this->loggedUser->id)
            ->count();
        $myAllActiveTasks = ProjectControl::withoutGlobalScopes()->where('applicable', 1)
            ->where('deadline', '>', date('Y-m-d'))
            ->where('status', '!=', 'Implemented')
            ->where('responsible', $this->loggedUser->id)->count();

        // queries to show total my completed tasks
        $myAllTasksCount = ProjectControl::withoutGlobalScopes()->where('applicable', 1)
            ->where('responsible', $this->loggedUser->id)
            ->count();
        $myCompletedTasksCount = ProjectControl::withoutGlobalScopes()->where('applicable', 1)
            ->where('status', 'Implemented')
            ->where('responsible', $this->loggedUser->id)
            ->count();

        $myCompletedTasksPercent = 0;

        if ($myCompletedTasksCount > 0 && $myAllTasksCount > 0) {
            $myCompletedTasksPercent = round($myCompletedTasksCount * 100 / $myAllTasksCount);
        }

        return [
            'totalUnderReviewMyTasks' => $totalUnderReviewMyTasks,
            'totalNeedMyApprovalTasks' => $totalNeedMyApprovalTasks,
            'totalTaskDueToday' => $totalTaskDueToday,
            'totalMyTaskPassDue' => $totalMyTaskPassDue,
            'myCompletedTasksPercent' => $myCompletedTasksPercent,
            'myAllActiveTasks' => $myAllActiveTasks,
        ];
    }

    public function getCalendarTask(Request $request)
    {
        $for_pdf=false;
        //calender task query
        if (isset($request->current_date_month) && !isset($request->date)) {
            $current_month=$request->current_date_month ;
            $next_month=Carbon::createFromFormat('Y-m-d', $current_month)->addMonth()->toDateString();
            $responsibleControls = ProjectControl::withoutGlobalScopes()
            ->select('compliance_project_controls.*',\DB::raw('true as new_controls'))
            ->where(function ($q) use ($current_month, $next_month) {
                $q->where('applicable', 1);
                $q->whereBetween('deadline', [$current_month,$next_month]);
                $q->where('responsible', $this->loggedUser->id);
                $q->whereNotNull('deadline');
            })->get();


            /**
             * fetch all the data that has frequency greater that One-Time
             * that does not have deadline on current month
             */
            $frequentResponsibleControls = ProjectControl::withoutGlobalScopes()
            ->select('compliance_project_controls.*',\DB::raw('false as new_controls'))
            ->where(function ($q) use ($current_month,$next_month){
                $q->where('applicable', 1);
                $q->where('frequency','!=','One-Time');
                $q->whereNotBetween('deadline',[$current_month,$next_month]);
                $q->where('responsible', $this->loggedUser->id);
                $q->whereNotNull('deadline');
            })->get();

            // If single request is done, cannot attach the deadline where query and all data are fetched
            // Two different query at least ignores unnecessary One-Time data of different dates in reference to current date
            $responsibleControls = $responsibleControls->merge($frequentResponsibleControls);



        } elseif (isset($request->date)) {
            $responsibleControls = ProjectControl::withoutGlobalScopes()->where(function ($q) use ($request) {
                $q->where('applicable', 1);
                $q->where('deadline', $request->date);
                $q->where('responsible', $this->loggedUser->id);
            })->paginate(10);
        } else {
            $for_pdf=true;
            $responsibleControls = ProjectControl::withoutGlobalScopes()->where(function ($q) {
                $q->where('applicable', 1);
                $q->where('responsible', $this->loggedUser->id);
                $q->whereNotNull('deadline');
            })->get();
        }
        $calendarTasks = [];

        $loop_count=0;
        $loopdate=[];
        $loop_latest_deadline=null;
        $number_of_event_to_take=$for_pdf?'x':'10';

        foreach ($responsibleControls as $responsibleControl) {
            if ($loop_latest_deadline!==$responsibleControl->deadline) {
                $loop_count=0;
            }
            $loop_latest_deadline=$responsibleControl->deadline;
            $current_date_count=$loop_latest_deadline.$loop_count;

            if ($current_date_count!==$loop_latest_deadline.$number_of_event_to_take && !in_array($loop_latest_deadline, $loopdate)) {
                $controlId = is_null($responsibleControl->id_separator) ? $responsibleControl->primary_id : $responsibleControl->primary_id.$responsibleControl->id_separator.$responsibleControl->sub_id;
                $taskStatusColor = '';
                $today = date('Y-m-d');
                $url = route('compliance-project-control-show', [$responsibleControl->project_id, $responsibleControl->id]);
                $frequency = $responsibleControl->frequency;
                // Not Implemented
                if (($responsibleControl->status == 'Not Implemented' || $responsibleControl->status == 'Rejected') && $responsibleControl->deadline >= $today) {
                    $taskStatusColor = '#414141'; //  Black
                } elseif ($responsibleControl->status == 'Under Review') {
                    // Under review
                $taskStatusColor = '#5bc0de'; //  Blue
                } elseif ($responsibleControl->deadline < $today && $responsibleControl->status != 'Implemented') {
                    // Late
                $taskStatusColor = '#cf1110'; //  Red
                } elseif ($responsibleControl->status == 'Implemented') {
                    // Completed
                $taskStatusColor = '#359f1d'; //  Green
                }

                // setting upcomming task date for task's with frequency other than `One-Time` task
                if ($frequency != 'One-Time') {
                    $nextReviewDate = '';
                    $currentMonth = new Carbon($request->current_date_month);
                    // $currentMonth->modify('28 days');
                    $currentDeadline = strtotime($responsibleControl->deadline);
                    $deadlineMonth = new Carbon(substr($responsibleControl->deadline,0,8).'01');
                    // for next year reflection
                    if($deadlineMonth->year < $currentMonth->year){
                        $date1 = strtotime($deadlineMonth->toDateString());
                        $date2 = strtotime($currentMonth->toDateString());
                        $deadlineDifference = 0;

                        while (($date1 = strtotime('+1 MONTH', $date1)) <= $date2)
                            $deadlineDifference++;
                    }
                    else{
                        $deadlineDifference = $currentMonth->month-$deadlineMonth->month;   
                    }
                    if($currentMonth->month>=$deadlineMonth->month || $deadlineMonth->year < $currentMonth->year ){
                            switch ($frequency) {
                                case 'Monthly':
                                    if($deadlineDifference == 0){

                                        $nextReviewDate = date('Y-m-d',$currentDeadline);
                                    }else{
                                        $nextReviewDate = date('Y-m-d', strtotime('+'.$deadlineDifference.' month', $currentDeadline));
                                    }
                                    break;
                                case 'Every 3 Months':
                                    if(!($deadlineDifference%3))
                                        $nextReviewDate = date('Y-m-d', strtotime('+'.$deadlineDifference.' month', $currentDeadline));
                                    break;
                                case 'Bi-Annually':
                                    if(!($deadlineDifference%6))
                                        $nextReviewDate = date('Y-m-d', strtotime('+'.$deadlineDifference.' month', $currentDeadline));
                                    break;
                                case 'Annually':
                                    if(!($deadlineDifference%12))
                                        $nextReviewDate = date('Y-m-d', strtotime('+'.$deadlineDifference.' month', $currentDeadline));
                                    break;
                            }
                            /**
                             * Set all the task to black
                             * if the frequency task is less than 14 days of deadline
                             * before 14 days frequency task are not activated
                             */
                            $upcoming_task_showed=false;
                            if(!$responsibleControl->new_controls){
                                $upcoming_task_showed=true;
                            }

                            if($today < date('Y-m-d',strtotime($nextReviewDate.' - 14 days') )){
                                $taskStatusColor = '#414141';
                            }
                            $calendarTasks[] = json_encode(['title' => decodeHTMLEntity($controlId).' '.decodeHTMLEntity($responsibleControl->name), 'start' => $nextReviewDate, 'backgroundColor' => $taskStatusColor, 'textColor' => '#fff', 'url' => $url, 'status' => $responsibleControl->status,'className'=>$upcoming_task_showed?'disabled_click':'']);
                        }
                }else{
                $calendarTasks[] = json_encode(['title' => addslashes(decodeHTMLEntity($controlId).' '.decodeHTMLEntity($responsibleControl->name)),'control_id'=>$responsibleControl->id, 'start' => $responsibleControl->deadline, 'backgroundColor' => $taskStatusColor, 'textColor' => '#fff', 'url' => $url, 'status' => $responsibleControl->status]);
                }

                $loop_count++;
            } else {
                array_push($loopdate, $loop_latest_deadline);
                $loop_count=0;
            }
        }
        $data['calendarTasks'] = $calendarTasks;
        if ($for_pdf) {
            return $calendarTasks;
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * getting the calendar more popover data
    */
    public function getCalendarMorePopoverData(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'page' => 'required'
        ]);

        $calendarTasks = [];
        $currrentPage = $request->page;
        $pageLength = 10;

        $responsibleControls = ProjectControl::withoutGlobalScopes()->where(function ($q) use ($request) {
            $q->where('applicable', 1);
            $q->where('deadline', $request->date);
            $q->where('responsible', $this->loggedUser->id);
        })->get();

        $frequencyControls = ProjectControl::withoutGlobalScopes()->where(function ($q) use ($request) {
            $q->where('applicable', 1);
            $q->where('frequency','!=','One-Time');
            $q->where('deadline','!=', $request->date);
            $q->whereDay('deadline',carbon::create($request->date)->day);
            $q->where('responsible', $this->loggedUser->id);
        })->get();

        $responsibleControls = $responsibleControls->merge($frequencyControls);


        foreach ($responsibleControls as $key => $responsibleControl) {
            $controlId = is_null($responsibleControl->id_separator) ? $responsibleControl->primary_id : $responsibleControl->primary_id.$responsibleControl->id_separator.$responsibleControl->sub_id;
            $taskStatusColor = '';
            $today = date('Y-m-d');
            $url = route('compliance-project-control-show', [$responsibleControl->project_id, $responsibleControl->id]);
            $frequency = $responsibleControl->frequency;
            // Not Implemented
            if (($responsibleControl->status == 'Not Implemented' || $responsibleControl->status == 'Rejected') && $responsibleControl->deadline >= $today) {
                $taskStatusColor = '#414141'; //  Black
            } elseif ($responsibleControl->status == 'Under Review') {
                // Under review
            $taskStatusColor = '#5bc0de'; //  Blue
            } elseif ($responsibleControl->deadline < $today && $responsibleControl->status != 'Implemented') {
                // Late
            $taskStatusColor = '#cf1110'; //  Red
            } elseif ($responsibleControl->status == 'Implemented') {
                // Completed
            $taskStatusColor = '#359f1d'; //  Green
            }

            // setting upcomming task date for task's with frequency other than `One-Time` task
            if ($frequency != 'One-Time') {
                $nextReviewDate = '';
                $currentMonth = new Carbon($request->current_date_month);
                $currentMonth->modify('28 days');
                $currentDeadline = strtotime($responsibleControl->deadline);
                $deadlineMonth = new Carbon(substr($responsibleControl->deadline,0,8).'01');
                $deadlineDifference = $currentMonth->diffInMonths($deadlineMonth);

                switch ($frequency) {
                    case 'Monthly':
                        if($deadlineDifference == 0){

                            $nextReviewDate = date('Y-m-d',$currentDeadline);
                        }else{
                            $nextReviewDate = date('Y-m-d', strtotime('+'.$deadlineDifference.' month', $currentDeadline));
                        }
                        break;
                    case 'Every 3 Months':
                        if(!($deadlineDifference%3))
                            $nextReviewDate = date('Y-m-d', strtotime('+'.$deadlineDifference.' month', $currentDeadline));
                        break;
                    case 'Bi-Annually':
                        if(!($deadlineDifference%6))
                            $nextReviewDate = date('Y-m-d', strtotime('+'.$deadlineDifference.' month', $currentDeadline));
                        break;
                    case 'Annually':
                        if(!($deadlineDifference%12))
                            $nextReviewDate = date('Y-m-d', strtotime('+'.$deadlineDifference.' month', $currentDeadline));
                        break;
                }
                /**
                 * Set all the task to black
                 * if the frequency task is less than 14 days of deadline
                 * before 14 days frequency task are not activated
                 */
                if($today < date('Y-m-d',strtotime($nextReviewDate.' - 14 days') )){
                    $taskStatusColor = '#414141';
                }
                $calendarTasks[] = json_encode(['title' => decodeHTMLEntity($controlId).' '.decodeHTMLEntity($responsibleControl->name), 'start' => $nextReviewDate, 'backgroundColor' => $taskStatusColor, 'textColor' => '#fff', 'url' => $url, 'status' => $responsibleControl->status]);
            }else{
                $calendarTasks[] = json_encode(['title' => addslashes(decodeHTMLEntity($controlId).' '.decodeHTMLEntity($responsibleControl->name)),'control_id'=>$responsibleControl->id, 'start' => $responsibleControl->deadline, 'backgroundColor' => $taskStatusColor, 'textColor' => '#fff', 'url' => $url, 'status' => $responsibleControl->status]);
            }
        }


        if($currrentPage >1){
            $paginationArray = array_slice($calendarTasks,($currrentPage-1)*$pageLength,$pageLength);
        }else{
            $paginationArray = array_slice($calendarTasks,0,$pageLength);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'currentPage' => $currrentPage,
                'pageCount' => ceil((count($calendarTasks)/$pageLength)),
                'totalCount' => count($calendarTasks),
                'calendarTasks' => $paginationArray
            ]
        ]);
    }

    public function exportToPDF(Request $request)
    {
        $data = $this->loadDashboardData();
        $data['calendarTasks'] = $this->getCalendarTask($request);

        $calendarTasks = collect($data['calendarTasks'])->map(function ($item) {
            $item = json_decode($item);

            if (date_format(date_create($item->start), 'F') == date('F')) {
                return $item;
            }
        });

        $calendarTasks = $calendarTasks->filter(function ($value) {
            return !is_null($value);
        });

        $data['calendarTasks'] = $calendarTasks->groupBy('start');

        $pdf = \PDF::loadView('compliance.dashboard.pdf-report', $data);
        $pdf->setOptions([
            'enable-local-file-access' => true,
            'enable-javascript' => true,
            'javascript-delay' => 5000,
            'enable-smart-shrinking' => true,
            'no-stop-slow-scripts' => true,
            'header-center' => 'Note: This is a system generated report',
            'footer-center' => 'Compliance Report',
            'footer-left' => 'Confidential',
            'footer-right' => '[page]',
        ]);

        Log::info('User has downloaded a compliance report.');

        return $pdf->download('compliance-report.pdf');
    }
}
