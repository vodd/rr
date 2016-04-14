<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;
class Client extends Model
{
    public $fillable = ['name','firstname','cv','sexe','daten','tel','add','sit','email','permi','armee','auto','info','dipo','exp','word','photoshop','powerpoint','autre','outlook','autocad','category_id','wilaya','cat1','cat2','cat3','histori',"spe"];

    public function Categories()
	{
	    return $this->belongsTo('App\Category');
	}

	public function Entrepises()
	{
	    return $this->belongsToMany('App\Entrepise');
	}

	use SearchableTrait;

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        'columns' => [
            'clients.name' => 10,
            'clients.firstname' => 10,
            'clients.tel' => 10,
            'clients.sexe' => 10,
            'clients.avatar' => 10000,
            'clients.email' => 10,
            'clients.wilaya' => 10,
            'clients.statut' => 2,
            'categories.name' => 10,
        ],
        'joins' => [
            'categories' => ['clients.category_id','categories.id'],
        ],
    ];
}
