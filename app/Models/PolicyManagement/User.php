<?php

namespace App\Models\PolicyManagement;

use App\Models\DataScope\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Casts\CleanHtml;

class User extends BaseModel
{
    protected $table = 'policy_users';
    protected $fillable = ['email', 'first_name', 'last_name', 'status'];

    protected $casts = [
        'first_name' => CleanHtml::class,
        'last_name' => CleanHtml::class,
        'email' => CleanHtml::class
    ];
}
