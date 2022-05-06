<?php

namespace App\Models\ThirdPartyRisk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;

    protected $table = 'third_party_domains';
    protected $fillable = ['name', 'order_number'];

}
