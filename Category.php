<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	public $fillable = ['name'];
    public function client()
	{
	    return $this->hasMany('App\Client');
	}
}
