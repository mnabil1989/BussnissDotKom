<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Service extends Model
{
          use Favoritable,Reviewable;

    	public function category() {
		return $this->belongsTo(Category::class);
	}
}
