<?php

namespace App\Models\Mfa;

use Illuminate\Database\Eloquent\Model;

class ResetMfa extends Model
{
    // public $timestamps = false;
    protected $table = 'mfa_resets';
    protected $fillable = ['email', 'token'];


    public function getUpdatedAtColumn() {
        return null;
    }

}
