<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Redirection extends Model
{
    protected $dates = ['created_at', 'updated_at' ];
    protected $fillable = [ 'code', 'slug_origin', 'slug_destine' ];

    public function getCreatedAtAttribute($date){
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
    }
}
