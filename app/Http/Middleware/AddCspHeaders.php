<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Csp\PolicyFactory;
use App\Security\Csp\NovaPolicies;
use Illuminate\Support\Collection;

class AddCspHeaders
{
    public function handle(Request $request, Closure $next, $customPolicyClass = null)
    {
        $response = $next($request);

        $this
            ->getPolicies($customPolicyClass,$request)
            ->filter->shouldBeApplied($request, $response)
            ->each->applyTo($response);

        return $response;
    }

    protected function getPolicies(string $customPolicyClass = null,$request): Collection
    {
        $policies = collect();
        
        if ($customPolicyClass) {
            $policies->push(PolicyFactory::create($customPolicyClass));

            return $policies;
        }

        if(str_contains($request->getPathInfo(),'nova')){
            $policies->push(PolicyFactory::create(NovaPolicies::class));
        }
        else{
            $policyClass = config('csp.policy');

            if (! empty($policyClass)) {
                $policies->push(PolicyFactory::create($policyClass));
            }
        }

        $reportOnlyPolicyClass = config('csp.report_only_policy');

        if (! empty($reportOnlyPolicyClass)) {
            $policy = PolicyFactory::create($reportOnlyPolicyClass);

            $policy->reportOnly();

            $policies->push($policy);
        }
       

        return $policies;
    }
}
