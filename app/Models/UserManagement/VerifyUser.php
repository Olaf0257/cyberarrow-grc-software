<?php

namespace App\models\UserManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserManagement\Admin;

class VerifyUser extends Model
{
    protected $fillable = ['user_id', 'token'];

    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }
}
