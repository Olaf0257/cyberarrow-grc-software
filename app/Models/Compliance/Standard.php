<?php

namespace App\Models\Compliance;

use Database\Factories\ComplianceStandardFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Mews\Purifier\Casts\CleanHtml;

class Standard extends Model
{
    use HasFactory;

    protected $table = 'compliance_standards';
    protected $fillable = ['name', 'slug', 'version', 'is_default'];

    protected $casts = [
       'name'    => CleanHtml::class,
       'version'    => CleanHtml::class,
   ];

    public function controls()
    {
        return $this->hasMany(StandardControl::class, 'standard_id');
    }

    public function projects()
    {
        return $this->hasMany('App\Models\Compliance\Project', 'standard_id');
    }


    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return ComplianceStandardFactory::new();
    }
}
