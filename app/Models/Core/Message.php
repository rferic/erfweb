<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [ 'message_parent_id', 'author_id', 'receiver_id', 'subject', 'text', 'status', 'tag' ];

    public function author ()
    {
        return $this->belongsTo(User::class, 'author_id')->with('roles');
    }

    public function receiver ()
    {
        return $this->belongsTo(User::class, 'receiver_id')->with('roles');
    }

    public function parent ()
    {
        return $this->belongsTo(Message::class, 'message_parent_id');
    }

    public function childs ()
    {
        return $this->hasMany(Message::class, 'message_parent_id');
    }

    public function isParent ()
    {
        return $this->parent === NULL;
    }

    public function hasChilds ()
    {
        return COUNT($this->childs) > 0;
    }

    public function isAuthor ()
    {
        return $this->author->id === auth()->id();
    }
}
