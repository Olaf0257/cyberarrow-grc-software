<?php

namespace App\Models\GlobalSettings;

use Illuminate\Database\Eloquent\Model;
use \Mews\Purifier\Casts\CleanHtml;

class LdapSetting extends Model
{
    protected $table = 'ldap_settings';

    protected $fillable = [
        'hosts',
        'base_dn',
        'username',
        'password',
        'port',
        'use_ssl',
        'version',
        'map_first_name_to',
        'map_last_name_to',
        'map_email_to',
        'map_contact_number_to'
    ];

    protected $casts = [
        'hosts'    => CleanHtml::class,
        'username'    => CleanHtml::class,
        'port'    => CleanHtml::class,
        'version'    => CleanHtml::class,
        'base_dn'    => CleanHtml::class,
        'map_first_name_to' => CleanHtml::class,
        'map_email_to' => CleanHtml::class,
        'map_last_name_to' => CleanHtml::class,
        'map_contact_number_to' => CleanHtml::class
    ];
}
