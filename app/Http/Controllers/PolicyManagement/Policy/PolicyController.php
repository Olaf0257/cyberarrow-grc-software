<?php

namespace App\Http\Controllers\PolicyManagement\Policy;

use App\Rules\ValidateUrlOrNetworkFolder;
use Inertia\Inertia;
use App\Rules\UniqueIf;
use Illuminate\Http\Request;
use App\Rules\ValidDataScope;
use App\Utils\RegularFunctions;
use App\Models\DataScope\Scopable;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\PolicyManagement\Policy;
use Illuminate\Support\Facades\Storage;
use App\Rules\Compliance\ControlLinkEvidences;
use App\Rules\PolicyManagement\UniquePolicyName;
use App\Rules\PolicyManagement\LinkPolicyUniqueName;

class PolicyController extends Controller
{
    protected $viewPath = 'policy-management.policy.';

    public function index(Request $request)
    {
        return Inertia::render('policy-management/policy/Policies');
    }

    /**
     * return the list of policies created
     */
    public function policyList(Request $request)
    {
        $page = $request->page ?? 1;
        $size = $request->per_page ?? 10;
        $keyword = $request->input('search');

        $policies = Policy::skip(--$page * $size)
            ->when($keyword, function ($query, $keyword) {
                return $query
                    ->where('display_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('description', 'LIKE', '%' . $keyword . '%');
            })
            ->take($size)
            ->paginate($size);
        $policies->getCollection()->transform(function ($item) {
            $item['action'] = null;
            return $item;
        });

        return response()->json([
            'data' => $policies
        ], 200);
    }

    public function getJsonData(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $draw = $request->draw;
        $search = $request->search['value'];

        $queryBuilder = Policy::where(function ($query) use ($search) {
            if ($search) {
                $query->where('display_name', 'like', '%' . $search . '%');
            }
        })->orderBy('id', 'DESC');
        $count = $queryBuilder->count();
        $policies = $queryBuilder->offset($start)->take($length)->get();
        $render = [];

        foreach ($policies as $key => $policy) {
            $actions = "<div class='btn-group'>";

            if ($policy->type == 'doculink') {
                $actions .= "<a class='btn btn-secondary btn-xs waves-effect waves-light' href='" . $policy->path . "' title= 'Link' target='_blank' ><i class='fe-link'></i></a>";
            } else {
                $actions .= "
                    <a class='btn btn-secondary btn-xs waves-effect waves-light' href='" . route('policy-management.policies.download-policies', $policy->id) . "' title= 'Download'>
                        <i class='fe-download'></i>
                    </a>
                ";
            }

            $actions .= "
                <a class='edit-action btn btn-info btn-xs waves-effect waves-light' data-get-policy-route='" . route('policy-management.policies.get-policy-data', $policy->id) . "' href='" . route('policy-management.policies.update-policies', $policy->id) . "' title= 'Edit Information' data-type='" . $policy->type . "' data-policy='" . json_encode($policy) . "'>
                    <i class='fe-edit'></i>
                </a>
            ";

            $actions .= " <a href='" . route('policy-management.policies.delete-policies', $policy->id) . "' class='policy-delete-link btn btn-danger btn-xs waves-effect waves-light' title= 'Delete'><i class='fe-trash-2'></i></a>";

            $actions .= '</div>';

            $render[] = [
                $policy->display_name,
                $policy->description,
                $policy->version,
                date('j M Y', strtotime($policy->created_at)),
                date('j M Y', strtotime($policy->updated_at)),
                $actions,
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $render,
        ]);
    }

    public function storeLinkPolicies(Request $request)
    {
        $request->validate([
            'display_name' => 'nullable|array|min:2',
            'display_name.*' => ['nullable', new LinkPolicyUniqueName],
            'link.*' => ['nullable', new ValidateUrlOrNetworkFolder],
            'version.*' => 'nullable',
            'description.*' => 'nullable',
        ]);

        $createdPolicies = [];

        foreach ($request->display_name as $key => $value) {
            if ($value && $request->link[$key] && $request->version[$key] && $request->description[$key]) {
                $currentDateTime = (new \DateTime())->format('Y-m-d H:i:s');
                $createdPolicies[] = Policy::create([
                    'display_name' => $value,
                    'type' => 'doculink',
                    'path' => $request->link[$key],
                    'version' => $request->version[$key],
                    'description' => $request->description[$key],
                    'created_at' => $currentDateTime,
                    'updated_at' => $currentDateTime,
                ]);
            }
        }

        if (count($createdPolicies) == 0) {
            return redirect()->back()->with('error', 'Please fill at least one row.');
        }

        return redirect()->back()->with('success', 'Policies inserted successfully.');
    }


    public function uploadFile(Request $request)
    {
        $request->validate(
            [
                'policy_file' => 'required|file|max:10240|mimes:jpeg,png,jpg,pdf,gif',
            ],
            [
                'policy_file.required' => 'The filetype is not supported',
            ]
        );

        $policyFile = $request->policy_file;

        if ($policyFile) {
            \DB::transaction(function () use ($policyFile) {
                $fileName = $policyFile->getClientOriginalName();

                $uploadedPolicy = Policy::create([
                    'display_name' => null,
                    'type' => 'document',
                    'path' => $fileName,
                    'version' => null,
                    'description' => null,
                ]);

                $filePath = "policy-management/policies/{$uploadedPolicy->id}";
                // Store the Content
                Storage::putFileAs('public/'.$filePath, $policyFile, $fileName,'public');

                $uploadedPolicy->update([
                    'path' => $filePath . '/' . $fileName,
                ]);
            });
        }

        return response()->json([
            'success' => true,
        ]);
    }


    public function uploadPolicies(Request $request)
    {
        $request->validate([
            'policy_file' => 'required|file|max:10240|mimes:jpeg,png,jpg,pdf,gif',
            'display_name' => ['required', 'string', new UniquePolicyName($request->get('version'))],
            'version' => 'required|string',
            'description' => 'required|string',
        ], [
            'policy_file.required' => 'The filetype is not supported',
        ]);

        $displayName = $request->display_name;
        $version = $request->version;
        $description = $request->description;
        $policyFile = $request->policy_file;

        if ($policyFile && $displayName && $version && $description) {
            $uploadedPolicy = \DB::transaction(function () use ($displayName, $version, $description, $policyFile) {
                $fileName = $policyFile->getClientOriginalName();

                $uploadedPolicy = Policy::create([
                    'display_name' => $displayName,
                    'type' => 'document',
                    'path' => $fileName,
                    'version' => $version,
                    'description' => $description,
                ]);

                $filePath = "policy-management/policies/{$uploadedPolicy->id}";

                // Store the Content
                Storage::putFileAs('public/'.$filePath, $policyFile, $fileName,'public');

                $uploadedPolicy->update([
                    'path' => $filePath . '/' . $fileName,
                ]);

                return $uploadedPolicy;
            });

            Log::info('User has uploaded a policy.', ['policy_id' => $uploadedPolicy->id]);

            return response()->json([
                'success' => true,
            ]);
        }
        return response()->json([
            'success' => false,
        ]);
    }

    public function downloadPolicies(Request $request, $id)
    {
        $policy = Policy::findOrFail($id);
        $baseName = basename($policy->path);

        $contents = Storage::get('public/'.$policy->path);

        Log::info('User has downloaded a policy.', ['policy_id' => $id]);

        return response()->streamDownload(function () use ($contents) {
            echo $contents;
        }, $baseName);
    }

    public function deletPolicies(Request $request, $id)
    {
        $policy = Policy::findOrFail($id);

        if ($policy->type == 'document') {
            $directory = 'public/policy-management/policies/' . $id;
            Storage::deleteDirectory($directory);
        }

        $policy->delete();
        Log::info('User has deleted a policy.', ['policy_id' => $id]);

        return redirect()->back()->with('success', 'Policy deleted successfully.');
    }

    public function updatePolicies(Request $request, $id)
    {
        $request->validate([
            'display_name' => ['required', 'string', new UniquePolicyName($request->get('version'), $id)],
            'version' => 'required',
            'description' => 'required',
            'policy_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf,gif',
        ], [
            'policy_file.nullable' => 'The filetype is not supported',
        ]);

        $policy = Policy::findOrFail($id);

        if ($policy->type == 'document') {
            if ($request->policy_file) {
                $policyFile = $request->policy_file;
                $fileName = $policyFile->getClientOriginalName();
                $filePath = "policy-management/policies/{$policy->id}";

                // deleting existing file
                Storage::delete('public/'.$policy->path);

                // Store the Content
                Storage::putFileAs('public/'.$filePath, $policyFile, $fileName,'public');

                $policy->fill([
                    'path' => $filePath . '/' . $fileName,
                ]);
            }
        } else {
            $policy->fill([
                'path' => $request->link,
            ]);
        }

        $policy->fill([
            'display_name' => $request->display_name,
            'version' => $request->version,
            'description' => $request->description,
        ]);

        $updated = $policy->update();

        if (!$updated) {
            return redirect()->back()->with('error', 'Oops something went wrong.');
        }
        Log::info('User has updated a policy.', ['policy_id' => $id]);
        return redirect()->back()->with('success', 'Policy updated successfully.');
    }

    public function getPolicyData(Request $request, $id)
    {
        $policy = Policy::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $policy,
        ]);
    }
}
