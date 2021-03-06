<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;


class Request extends Model
{
      use Favoritable, Translatable,Reviewable;
    protected $translatable = ['name', 'description'];
    	public function category() {
		return $this->belongsTo('App\Models\Category','category_id');
	}
    	public function user() {
		return $this->belongsTo('App\Models\User','user_id');
	}
}
