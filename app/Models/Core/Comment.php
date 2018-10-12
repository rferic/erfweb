<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Comment extends Model
{
    use SoftDeletes;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [ 'user_id', 'status', 'origin', 'subject', 'text' ];

    public function author ()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isAuthor ()
    {
        return $this->author->id === auth()->id();
    }

    public function getCreatedAtAttribute($date){
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
    }
}
