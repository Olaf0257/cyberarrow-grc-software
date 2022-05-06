<?php

namespace App\Utils;

class AccessControlHelpers
{
    public static function viewProjectControlDetails($user, $projectControl)
    {
        $allowed = false;

        $isApplicable = $projectControl->applicable;

        if ($isApplicable) {
            if ($user->hasAnyRole(['Global Admin', 'Compliance Administrator', 'Auditor'])) {
                $allowed = true;
            } else {
                $assignedProjectControl = $projectControl->where('approver', $user->id)->orWhere('responsible', $user->id)->first();

                if (!is_null($assignedProjectControl)) {
                    $allowed = true;
                }
            }
        }

        return  $allowed;
    }
}
