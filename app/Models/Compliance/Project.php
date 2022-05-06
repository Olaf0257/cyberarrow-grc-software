<?php

namespace App\Models\Compliance;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use App\Models\DataScope\BaseModel;
use App\Models\DataScope\Scopable;
use \Mews\Purifier\Casts\CleanHtml;

class Project extends BaseModel
{
    use HasFactory;

    protected $table = 'compliance_projects';
    protected $fillable = ['name', 'standard_id', 'standard', 'slug', 'description'];

    protected $casts = [
        'name'    => CleanHtml::class,
        'description'    => CleanHtml::class,
        'path'    => CleanHtml::class,
        'version'    => CleanHtml::class,

    ];


    public function controls()
    {
        return $this->hasMany(ProjectControl::class, 'project_id')
            ->orderBy(DB::raw('CAST(primary_id AS UNSIGNED), primary_id, CAST(sub_id AS UNSIGNED), sub_id'));
    }

    /**
     * returns the applicable controls
     */
    public function applicableControls()
    {
        return $this->hasMany(ProjectControl::class, 'project_id')->where('applicable', 1);
    }

    /**
     * returns the implemented control(s)
    */
    public function implementedControls()
    {
        return $this->hasMany(ProjectControl::class, 'project_id')->where('applicable', 1)->where('status', 'Implemented');
    }

    /**
     * returns the not implemented control(s)
    */
    public function notImplementedControls()
    {
        return $this->hasMany(ProjectControl::class, 'project_id')->where('applicable', 1)->where('status', 'Not Implemented');
    }

    /**
     * Method department
     *
     * @return void
     */
    public function department()
    {
        return $this->morphOne(Scopable::class, 'scopable');
    }

    public function of_standard()
    {
        return $this->belongsTo(Standard::class, 'standard_id');
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return ProjectFactory::new();
    }
}
