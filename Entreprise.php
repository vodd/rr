<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{

	
	public $fillable = ['name','tel','email','adr','type'];
    //

    public function Client()
	{
	    return $this->belongsToMany('App\Client')->withPivot('created_at','type','statut','poste');
	}
}
