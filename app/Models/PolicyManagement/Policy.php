<?php

namespace App\Models\PolicyManagement;

use Illuminate\Database\Eloquent\Model;
use \Mews\Purifier\Casts\CleanHtml;
use  App\Models\DataScope\BaseModel;


class Policy extends BaseModel
{
    protected $table = 'policy_policies';
    protected $fillable = ['display_name', 'type', 'path', 'version', 'description'];



    protected $casts = [
        'display_name'    => CleanHtml::class,
        'version'    => CleanHtml::class,
        'description'    => CleanHtml::class,
        'created_at' => 'datetime:jS F y',
        'updated_at' => 'datetime:jS F y'
    ];


}
