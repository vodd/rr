<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Entreprise;
use App\Client;
use App\Category;
use App\Task;
use Illuminate\Support\Facades\Input;
use Validator;
use Redirect;
use Session;
use Response;
use App\Http\Requests\FormRequest;

class EntreprisesController extends Controller
{
    public function index(){
    	$ent = Entreprise::paginate(15);
        return view('Entreprises.index',compact('ent'));
    }

    public function create(){
    	return view('Entreprises.create');
    }

    public function store(FormRequest $request){
    	$ent = new Entreprise();
    	$ent->name = $request->get('name');
    	$ent->tel = $request->get('tel');
        $ent->email = $request->get('email');
        $ent->adr = $request->get('adr');
    	$ent->type = $request->get('type');
        $ent->save();
        Session::flash('flash_message', 'Upload successfully');
      	return Redirect::action('EntreprisesController@create');
    }

    public function adc($client_id,$id,$category_id){
        $client = Client::findOrFail($client_id);
    	$cat = Category::find($category_id);
    	$ent = Entreprise::find($id);  
    	return view('Entreprises.adc',compact('client','ent','cat')); 

    }

    public function adp(Request $request){
        $ent = Entreprise::find($request->get('entreprise_id'));
        $ent->Client()->attach([$request->get('client_id')=>['created_at'=>$request->get('created_at'),'statut'=>$request->get('statut'),'poste'=>$request->get('poste')]]);
        $client = Client::find($request->get('client_id'));
        $client->statut = 1;
        $client->update();
        return redirect('entreprises/'.$request->get('entreprise_id'));
    }

    public function edit($id){
        $ent = Entreprise::find($id);
        return view('Entreprises.edit',compact('ent'));
    }

    public function update(Request $request, $id){
        $input = $request->all();
        $ent = Entreprise::find($id);
        $ent->update($input);

         Session::flash('flash_message', 'Task successfully added!');

         return redirect()->back();
    }
    public function show($id){
        $ent = Entreprise::findOrFail($id);
        $client = Entreprise::findOrFail($id)->client;
        return view('Entreprises.show',compact('ent','client'));

    }

    public function updst($id,$client_id){
        $client = Client::findOrFail($client_id);
        $cat = Category::find($client->category_id);
        $ent = Entreprise::find($id);  
        return view('Entreprises.updst',compact('client','ent','cat'));
    }
    public function delc(Request $request){
        $ent = Entreprise::find($request->id);
        Entreprise::find($request->id)->Client()->updateExistingPivot($request->client_id, ['type'=>$request->get('type'),'statut'=>$request->get('statut'),'created_at'=>$request->get('created_at')]);
        if($request->get('statut')== 'recruté'){
            $client = Client::find($request->client_id);
            $client->statut = 0;
            $client->update();
        }

        return redirect('entreprises/'.$request->get('entreprise_id'));
    }

    public function autocomplete(){
        $term = Input::get('term');
        
        $results = array();
        
        $queries = Entreprise::where('name', 'LIKE', '%'.$term.'%')            
            ->take(5)->get();
        
        foreach ($queries as $query)
        {
            $results[] = [ 'id' => $query->id, 'value' => $query->name ];
        }
    return Response::json($results);
    }

    public function blyat(){
        $client = Client::paginate(10);
        $ent = Entreprise::all();
        //$clients = $ent->Client-get();
        $tasks = Task::orderBy('created_at', 'desc')->get();
        return view('Entreprises.blyat',compact('client','tasks'));
    }
}
