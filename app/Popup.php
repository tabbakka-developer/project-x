<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Popup extends Model
{
    protected $fillable = [
    	'event_id',
	    'title',
	    'image',
	    'message',
	    'url_to'
    ];

    public function event() {
    	return $this->belongsTo(Event::class);
    }
}
