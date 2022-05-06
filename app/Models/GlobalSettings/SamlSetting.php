<?php

namespace App\Models\GlobalSettings;

use Illuminate\Database\Eloquent\Model;

class SamlSetting extends Model
{
    protected $table = 'saml_settings';
    protected $guarded = ['id'];
}
