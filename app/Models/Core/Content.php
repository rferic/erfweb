<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    use SoftDeletes;

    protected $fillable = [ 'page_locale_id', 'key', 'id_html', 'class_html', 'text', 'header_inject', 'footer_inject', 'priority'];
    protected $happens = ['page'];

    public function pageLocale ()
    {
        return $this->belongsTo(PageLocale::class, 'page_locale_id');
    }
    
    public function getPageAttribute ()
    {
        return $this->pageLocale->page;
    }
}
