<?php

namespace App\Models\UserManagement;

use LdapRecord\Models\Model;

class LdapUser extends Model
{
    // public static $objectClasses = [
    //     'top',
    //     'person',
    //     'organizationalperson',
    //     'user',
    // ];

    protected $connection = 'ldap';
}
