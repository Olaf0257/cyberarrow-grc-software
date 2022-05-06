<?php

namespace App\Http\Controllers\RisksManagement\Dashboard;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\DataScope\DataScope;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\RiskManagement\RiskCategory;
use App\Models\RiskManagement\RiskRegister;
use App\Models\RiskManagement\RiskMatrix\RiskScoreLevel;

class DashboardController extends Controller
{
    private $baseViewPath = 'risk-management.dashboard.';

    public function index()
    {
        return Inertia::render('risk-management/dashboard/Dashboard');
    }

    public function getDashboardDataJson(Request $request)
    {
        $data= $this->getDashboardData($request);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    private function getDashboardData($request)
    {
        if (isset($request->departments)) {
            $topTenRisks = RiskRegister::withoutGlobalScope(DataScope::class)->ofDepartment()->with('category')->orderBy('residual_score', 'DESC')->take(10)->get();
            $riskRegisterCategories = RiskCategory::whereHas('riskRegisterWithoutScope')->get();
        } else {
            $topTenRisks = RiskRegister::with('category')->orderBy('residual_score', 'DESC')->take(10)->get();
            $riskRegisterCategories = RiskCategory::whereHas('riskRegister')->get();
        }

        $riskLevels = RiskScoreLevel::whereHas('levelTypes', function ($query) {
            $query->where('is_active', 1);
        })->get();

        $riskRegisterCategoriesList = $riskRegisterCategories->pluck('name')->toArray();
        $riskCountWithinRiskLevelForCategories = [];
        $riskCountWithinRiskLevels = [];
        $riskLevelColors = [];
        $riskLevelsList = [];
        $closedRiskCountOfDifferentLevels = [];

        foreach ($riskLevels as $riskLevelIndex => $riskLevel) {
            $startScore =  $riskLevelIndex == 0 ? 0 : $riskLevels[$riskLevelIndex-1]->max_score + 1;
            $endScore = $riskLevels[$riskLevelIndex]->max_score;
            $isLastRiskLevelIndex = $riskLevels->keys()->last() == $riskLevelIndex;

            if (isset($request->departments)) {
                /* Creating risk count within different levels */
                /* closed Risk Of DifferentLevels */
                if (!$isLastRiskLevelIndex) {
                    $riskCountWithinRiskLevel = RiskRegister::withoutGlobalScope(DataScope::class)->ofDepartment()->whereBetween('residual_score', [$startScore, $endScore])->count();
                    $closedRiskCountOfDifferentLevels[] = RiskRegister::withoutGlobalScope(DataScope::class)->ofDepartment()->whereBetween('inherent_score', [$startScore, $endScore])->where('status', 'Close')->count();
                } else {
                    $riskCountWithinRiskLevel = RiskRegister::withoutGlobalScope(DataScope::class)->ofDepartment()->where('residual_score', '>=', $startScore)->count();
                    $closedRiskCountOfDifferentLevels[] = RiskRegister::withoutGlobalScope(DataScope::class)->ofDepartment()->where('inherent_score', '>=', $startScore)->where('status', 'Close')->count();
                }
            } else {
                /* Creating risk count within different levels */
                /* closed Risk Of DifferentLevels */
                if (!$isLastRiskLevelIndex) {
                    $riskCountWithinRiskLevel = RiskRegister::whereBetween('residual_score', [$startScore, $endScore])->count();
                    $closedRiskCountOfDifferentLevels[] = RiskRegister::whereBetween('inherent_score', [$startScore, $endScore])->where('status', 'Close')->count();
                } else {
                    $riskCountWithinRiskLevel = RiskRegister::where('residual_score', '>=', $startScore)->count();
                    $closedRiskCountOfDifferentLevels[] = RiskRegister::where('inherent_score', '>=', $startScore)->where('status', 'Close')->count();
                }
            }
            $riskCountWithinRiskLevels[] = [
                'name' => $riskLevel->name,
                'risk_count' => $riskCountWithinRiskLevel,
                'color' => $riskLevel->color
            ];

            /* risk count in each category within level*/
            $riskCountWithinRiskLevelForCategory = [
                'name' => $riskLevel->name,
                'data' => [],
                'color' => $riskLevel->color
            ];

            foreach ($riskRegisterCategories as $key => $riskRegisterCategory) {
                if (isset($request->departments)) {
                    if (!$isLastRiskLevelIndex) {
                        $riskCountWithinRiskLevelOfCategory = RiskRegister::withoutGlobalScope(DataScope::class)->ofDepartment()->where('category_id', $riskRegisterCategory->id)->whereBetween('residual_score', [$startScore, $endScore])->count();
                    } else {
                        $riskCountWithinRiskLevelOfCategory = RiskRegister::withoutGlobalScope(DataScope::class)->ofDepartment()->where('category_id', $riskRegisterCategory->id)->where('residual_score', '>=', $startScore)->count();
                    }
                } else {
                    if (!$isLastRiskLevelIndex) {
                        $riskCountWithinRiskLevelOfCategory = RiskRegister::where('category_id', $riskRegisterCategory->id)->whereBetween('residual_score', [$startScore, $endScore])->count();
                    } else {
                        $riskCountWithinRiskLevelOfCategory = RiskRegister::where('category_id', $riskRegisterCategory->id)->where('residual_score', '>=', $startScore)->count();
                    }
                }

                $riskCountWithinRiskLevelForCategory['data'][] = $riskCountWithinRiskLevelOfCategory;
            }

            $riskCountWithinRiskLevelForCategories[] = $riskCountWithinRiskLevelForCategory;


            /* Creating risks level color array*/
            $riskLevelColors[] = $riskLevel->color;

            /* setting risk level list*/
            $riskLevelsList[] = $riskLevel->name;
        }

        return [
            'topTenRisks' => $topTenRisks,
            'riskRegisterCategoriesList' => $riskRegisterCategoriesList,
            'riskCountWithinRiskLevelForCategories' => $riskCountWithinRiskLevelForCategories,
            'riskCountWithinRiskLevels' => $riskCountWithinRiskLevels,
            'riskLevelColors' => $riskLevelColors,
            'riskLevelsList' => $riskLevelsList,
            'closedRiskCountOfDifferentLevels' => $closedRiskCountOfDifferentLevels
        ];
    }

    public function getTopRisksJson(Request $request)
    {
        $page = $request->page ?? 1;
        $size = $request->per_page ?? 10;
        $keyword = $request->search ?? null;
        $start = ($page - 1) * $size;

        if (isset($request->departments)) {
            $risk_register = RiskRegister::withoutGlobalScope(DataScope::class)->ofDepartment()
                ->orderBy('residual_score', 'DESC')
                ->when($keyword, function ($query) use ($keyword) {
                    return $query->where('name', 'LIKE', $keyword . '%');
                });
        } else {
            $risk_register = RiskRegister::query()
            ->orderBy('residual_score', 'DESC')
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('name', 'LIKE', $keyword . '%');
            });
        }

        $count = $risk_register->count();
        $risk_register = $risk_register->with('category')->skip(--$page * $size)->take($size)->paginate($size);
        $risk_register->getCollection()->transform(function ($risk, $key) use ($start) {
            // add an index to each risk
            $risk['index'] = $key + $start + 1;
            return $risk;
        });
        return response()->json([
            'data' => $risk_register,
            'total' => $count,
        ], 200);
    }

    public function getTopRisks(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $draw = $request->draw;
        $count = 0;
        $render = [];

        $riskStatusClass = [
            'Mitigate' => 'bg-danger',
            'Accept' => 'bg-success',
            'Closed' => 'bg-warning'
        ];

        $sortColumns = $request->order;

        $topRiskQuery = RiskRegister::with('category');
        $count = $topRiskQuery->count();

        // for first time draw
        if ($draw == 1) {
            $topRiskQuery->orderBy('residual_score', 'DESC');
        }

        // sort by residual risk score
        if ($sortColumns[0]['column'] == 8) {
            $topRiskQuery->orderBy('residual_score', $sortColumns[0]['dir']);
        }

        // sort by inherent risk score
        if ($sortColumns[0]['column'] == 7) {
            $topRiskQuery->orderBy('inherent_score', $sortColumns[0]['dir']);
        }

        // sort by treatment_options
        if ($sortColumns[0]['column'] == 4) {
            $topRiskQuery->orderBy('treatment_options', $sortColumns[0]['dir']);
        }

        // sort by id
        if ($sortColumns[0]['column'] == 0) {
            $topRiskQuery->orderBy('id', $sortColumns[0]['dir']);
        }

        // sort by name or title
        if ($sortColumns[0]['column'] == 1) {
            $topRiskQuery->orderBy('name', $sortColumns[0]['dir']);
        }

        $topRisks = $topRiskQuery->offset($start)->take($length)->get();


        // sorting on collection after query, suited for computed attributes
        // sort by category name
        if ($sortColumns[0]['column'] == 2) {
            if ($sortColumns[0]['dir'] == "asc") {
                $topRisks = $topRisks->sortBy('category.name');
            } else {
                $topRisks = $topRisks->sortByDesc('category.name');
            }
        }

        // sort by likelihood
        if ($sortColumns[0]['column'] == 5) {
            if ($sortColumns[0]['dir'] == "asc") {
                $topRisks = $topRisks->sortBy('likelihood');
            } else {
                $topRisks = $topRisks->sortByDesc('likelihood');
            }
        }

        // sort by impact
        if ($sortColumns[0]['column'] == 6) {
            if ($sortColumns[0]['dir'] == "asc") {
                $topRisks = $topRisks->sortBy('impact');
            } else {
                $topRisks = $topRisks->sortByDesc('impact');
            }
        }

        // sort by status
        if ($sortColumns[0]['column'] == 3) {
            if ($sortColumns[0]['dir'] == "asc") {
                $topRisks = $topRisks->sortBy('status');
            } else {
                $topRisks = $topRisks->sortByDesc('status');
            }
        }



        // building data to be renders in datatable
        $i = $start;
        foreach ($topRisks as $topRisk) {
            $status = '<span class="badge bg-danger rounded-pill">Open</span>';

            if ($topRisk->status == 'Close') {
                $status = '<span class="badge bg-success rounded-pill">Closed</span>';
            }

            $render[] = [
                ++$i,
                $topRisk->name,
                $topRisk->category->name,
                $status,
                '<span class="badge'.$riskStatusClass[$topRisk->treatment_options].' rounded-pill">'.$topRisk->treatment_options.'</span>',
                $topRisk->riskMatrixLikelihood ?  $topRisk->riskMatrixLikelihood->name : '', // computed likelihood
                $topRisk->riskMatrixImpact ? $topRisk->riskMatrixImpact->name : '', // computed impact
                $topRisk->inherent_score, // inherent risk score
                $topRisk->residual_score, // residual scrore
                '<a href="'.route('risks.register.risks-show', $topRisk->id) .'" class="btn btn-primary btn-view btn-sm width-sm">View</a>'
            ];
        }

        $response = array(
            'draw' => $draw,
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $render,
        );

        return response()->json($response);
    }

    public function generatePdfReport(Request $request)
    {
        $data = $this->getDashboardData($request);
        // review report
        // return view($this->baseViewPath.'pdf-report', $data);
        $pdf = \PDF::loadView('risk-management.dashboard.pdf-report', $data);
        
        $pdf->setOptions([
            'enable-local-file-access' => true,
            'enable-javascript' => true,
            'javascript-delay' => 3000,
            'enable-smart-shrinking' =>  true,
            'no-stop-slow-scripts' => true,
            'header-center' => 'Note: This is a system generated report',
            'footer-center' => 'Risk Report',
            'footer-left' => 'Confidential',
            'footer-right' => '[page]',
            'debug-javascript' => true,
        ]);

        Log::info('User has downloaded a risks report.');

        return $pdf->inline('risks-report.pdf');
    }
}
