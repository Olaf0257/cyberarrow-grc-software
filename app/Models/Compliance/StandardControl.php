<?php

namespace App\Models\Compliance;

use Illuminate\Database\Eloquent\Model;
use \Mews\Purifier\Casts\CleanHtml;

class StandardControl extends Model
{

    protected $table = 'compliance_standard_controls';
    protected $fillable = ['name', 'standard_id', 'slug', 'description', 'primary_id', 'sub_id', 'id_separator','required_evidence'];
    protected $appends = ['controlId', 'idSeparators'];


    protected $casts = [
        'name'    => CleanHtml::class,
        'description'    => CleanHtml::class,
        'required_evidence'    => CleanHtml::class,
        'primary_id'    => CleanHtml::class,
        'sub_id'    => CleanHtml::class,
    ];


    public function standard()
    {
        return $this->belongsTo(Standard::class, 'standard_id');
    }

    public function getControlIdAttribute()
    {
        $controlId = null;

        if (!is_null($this->id_separator)) {
            $separatorId = ($this->id_separator == '&nbsp;') ? ' ' : $this->id_separator;

            $controlId = $this->primary_id.$separatorId.$this->sub_id;
        } else {
            $controlId = $this->primary_id.$this->sub_id;
        }

        return $controlId;
    }

    public function getIdSeparatorsAttribute()
    {
        return [
            '.' => 'Dot Separated',
            '&nbsp;' => 'Space Separated',
            '-' => 'Dash Separated',
            ',' => 'Comma Separated',
        ];
    }

    /**
     * Get the non breaking space if value is space.
     *
     * @return string
     */
    public function getIdSeparatorAttribute($value)
    {
        if ($value == ' ') {
            return '&nbsp;';
        }

        return $value;
    }

    /**
     * Set the id_separator to space if value is non-breaking space.
     *
     * @param string $value
     *
     * @return void
     */
    public function setIdSeparatorAttribute($value)
    {
        if ($value == '&nbsp;') {
            $this->attributes['id_separator'] = ' ';
        } else {
            $this->attributes['id_separator'] = $value;
        }
    }


}
