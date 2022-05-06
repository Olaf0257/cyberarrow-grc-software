<?php

namespace App\Http\Controllers\ThirdPartyRisk;

use App\Http\Controllers\Controller;
use App\Models\ThirdPartyRisk\Project;
use App\Models\ThirdPartyRisk\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class DashboardController extends Controller
{

    public function __construct() {
        $this->middleware('data_scope')->except('index');
    }

    public function index(){
        return Inertia::render('third-party-risk/dashboard/Index');
    }

    public function getVendorsData (Request $request)
    {
        $data = $this->getVendorsMaturity($request);
        $levels = $data['levels'];
        $projects_progress = $data['projects_progress'];

        return response()->json([
            "levels" => $levels,
            "projects_progress" => $projects_progress,
        ]);
    }

    public function getVendorsMaturity($request)
    {
        $vendors = Vendor::whereHas('projects')->get();

        $levels = [
            [
                'level' => 1,
                'color' => '#ff0000',
                'name' => 'Level 1',
                'count' => $vendors->where('level', 1)->count()
            ],
            [
                'level' => 2,
                'color' => '#ffc000',
                'name' => 'Level 2',
                'count' => $vendors->where('level', 2)->count()
            ],
            [
                'level' => 3,
                'color' => '#ffff00',
                'name' => 'Level 3',
                'count' => $vendors->where('level', 3)->count()
            ],
            [
                'level' => 4,
                'color' => '#92d050',
                'name' => 'Level 4',
                'count' => $vendors->where('level', 4)->count()
            ],
            [
                'level' => 5,
                'color' => '#00b050',
                'name' => 'Level 5',
                'count' => $vendors->where('level', 5)->count()
            ],
        ];

        $projects = Project::get();
        $campaign_stats = 'project_status.status';
        $projects_progress = [
            "Not Started" => $projects->where($campaign_stats, "Not Started")->count(),
            "In Progress" => $projects->where($campaign_stats, "In Progress")->count(),
            "Completed" => $projects->where($campaign_stats, "Completed")->count(),
            "Overdue" => $projects->where($campaign_stats, "Overdue")->count(),
        ];

        $projects_progress_pdf = [
            [
                'level' => 'Not Started',
                'color' => 'rgb(65, 65, 65)',
                'name' => 'Not Started',
                'count' => $projects->where($campaign_stats, "Not Started")->count()
            ],
            [
                'level' => "In Progress",
                'color' => 'rgb(91, 192, 222)',
                'name' => "In Progress",
                'count' => $projects->where($campaign_stats, "In Progress")->count()
            ],
            [
                'level' =>  "Completed",
                'color' => 'rgb(53, 159, 29)',
                'name' =>  "Completed",
                'count' => $projects->where($campaign_stats, "Completed")->count()
            ],
            [
                'level' => "Overdue",
                'color' => 'rgb(207, 17, 16)',
                'name' => "Overdue",
                'count' => $projects->where($campaign_stats, "Overdue")->count()
            ]
        ];

        return [
            'levels' => $levels,
            'projects_progress' => $projects_progress,
            'projects_progress_pdf'=>$projects_progress_pdf
        ];
    }

    public function getTopVendors(Request $request)
    {
        $data = $this->topVendors($request);

        return response()->json(['data' => $data]);
    }

    public function topVendors($request, $paginate = true){
        $vendors_query = Vendor::query()->whereHas('projects')->with('latestProject');
        $per_page = 10;

        if ($request->has('search')) {
            $vendors_query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->has('per_page')) {
            $per_page = $request->per_page;
        }

        if($paginate){
            $data = $vendors_query->orderByDesc('score')->paginate($per_page);
        } else {
            $data = $vendors_query->orderByDesc('score')->limit($per_page)->get();
        }

        return $data;
    }

    public function exportPDF(Request $request){
        $vendors_maturity_data = $this->getVendorsMaturity($request);
        $top_vendors = $this->topVendors($request, false);

        $data = $vendors_maturity_data;
        $data["top_vendors"] = $top_vendors;

        $pdf = \PDF::loadView('third-party-risks.dashboard-pdf-report', $data);
        $pdf->setOptions([
            'enable-local-file-access' => true,
            'enable-javascript' => true,
            'javascript-delay' => 3000,
            'enable-smart-shrinking' =>  true,
            'no-stop-slow-scripts' => true,
            'header-center' => 'Note: This is a system generated report',
            'footer-center' => 'Third Party Risk Report',
            'footer-left' => 'Confidential',
            'footer-right' => '[page]',
            'debug-javascript' => true,
        ]);

        Log::info('User has downloaded a third party risk dashboard report as pdf.');
        return $pdf->inline('third-party-risk.pdf');
    }
}
