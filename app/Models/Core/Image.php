<?php

namespace App\Models\Core;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $dates = ['created_at', 'updated_at'];
    protected $fillable = [ 'src', 'title' ];

    public function getCreatedAtAttribute($date){
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
    }
}
