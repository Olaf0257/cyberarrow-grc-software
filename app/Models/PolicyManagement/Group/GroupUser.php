<?php

namespace App\Models\PolicyManagement\Group;

use App\Models\DataScope\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Casts\CleanHtml;

class GroupUser extends BaseModel
{
    protected $table = 'policy_group_users';
    protected $fillable = ['first_name', 'last_name', 'email'];

    protected $casts = [
        'first_name' => CleanHtml::class,
        'last_name' => CleanHtml::class,
        'email' => CleanHtml::class
    ];
}
