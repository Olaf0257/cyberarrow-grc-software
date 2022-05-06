<?php

namespace App\Models\ThirdPartyRisk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    use HasFactory;

    protected $table = 'third_party_industries';
    protected $fillable = ['name', 'order_number'];

}
