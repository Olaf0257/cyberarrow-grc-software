<?php

namespace App\Models\GlobalSettings;

use Illuminate\Database\Eloquent\Model;
use \Mews\Purifier\Casts\CleanHtml;
class MailSetting extends Model
{

    protected $table = 'mail_settings';
    protected $guarded = ['id'];


    protected $casts = [
        'mail_driver'    => CleanHtml::class,
        'mail_host'    => CleanHtml::class,
        'mail_from_address'    => CleanHtml::class,
        'mail_from_name'    => CleanHtml::class,
    ];
}
