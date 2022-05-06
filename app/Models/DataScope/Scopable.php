<?php

namespace App\Models\DataScope;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scopable extends Model
{
    use HasFactory;
    
    protected $fillable = ['organization_id','department_id', 'scopable_id', 'scopable_type'];
    
}
