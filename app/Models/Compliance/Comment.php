<?php

namespace App\Models\Compliance;

use Illuminate\Database\Eloquent\Model;
use \Mews\Purifier\Casts\CleanHtml;

class Comment extends Model
{


    protected $table = 'compliance_project_control_comments';
    protected $fillable = ['project_control_id', 'from', 'to', 'comment'];

    public function sender()
    {
        return $this->belongsTo('App\Models\UserManagement\Admin', 'from');
    }

    public function receiver()
    {
        return $this->belongsTo('App\Models\UserManagement\Admin', 'to');
    }

    public function setCommentAttribute($value){
        $this->attributes['comment'] = clean($value);
    }

    public function getCommentAttribute($value){
        return nl2br($value);
    }

}
