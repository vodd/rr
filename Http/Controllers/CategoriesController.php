<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Category;
use Illuminate\Support\Facades\Input;
use Validator;
use Redirect;
use Session;
use Response;

class CategoriesController extends Controller
{
    public function index(){
        $cat = Category::all();
        return view('Categories.index',compact('cat'));
    }
    public function show($id){
    	$dd = Category::find($id)->client;
    	dd($dd);
    }

    public function create(){
    	return view('Categories.create');
    }

    public function store(Request $request){
    	$cat = new Category();
    	$cat->name = $request->get('name');
        $cat->save();
        Session::flash('success', 'Upload successfully');
      	return Redirect::action('CategoriesController@create');
    }
    public function edit($id){
        $cat = Category::findOrFail($id);
        return view('Categories.edit',compact('cat'));
    }

    public function update(Request $request){
        $cat = Category::findOrFail($request->input('id'));
        $input = $request->all();
        $cat->update($input);

        Session::flash('flash_message', 'Task successfully added!');

        return redirect()->back();
    }

    public function autocomplete(){
        $term = Input::get('term');
        
        $results = array();
        
        $queries = Category::where('name', 'LIKE', '%'.$term.'%')            
            ->take(5)->get();
        
        foreach ($queries as $query)
        {
            $results[] = [ 'id' => $query->id, 'value' => $query->name ];
        }
    return Response::json($results);
    }


}
