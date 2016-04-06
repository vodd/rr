<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Input;
use Validator;
use Redirect;
use Session;
use DB;
use App\Client;
use App\Category;
use App\Task;
use Ddeboer\Tesseract\Tesseract;
use XPDF;
class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    $client = Client::all();
    // returns a view and passes the view the list of articles and the original query.
    return view('client.index',compact('client'));


    }

    public function search(Request $request){
    $query = $request->get('search');

    $articles = Client::search($query)
            ->get();


    return view('client.search',compact('articles'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('client.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $client = new Client();
      if ($request->file('cv')) {
      $destinationPath = 'cv'; // upload path
      $extension = $request->file('cv')->getClientOriginalExtension();
      $fileName = $request->get('name').'-'.$request->get('firstname').'.'.$extension; // renameing image
      $request->file('cv')->move($destinationPath, $fileName); // uploading file to given path
      $client->cv = $fileName;
      $client->name = $request->get('name');
      $client->firstname = $request->get('firstname');
      $client->save();


      if($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png'){
         $tesseract = new Tesseract();
        $text = $tesseract->recognize('cv/'.$fileName);
        $client->avatar = $text;
        $client->save();
      }
      elseif($extension == 'pdf'){
        $filename = 'cv/'.$fileName;
        $pdfToText = XPDF\PdfToText::create();
        $text = $pdfToText->getText($filename);
        $client->avatar = $text;
        $client->save();
      }elseif($extension == 'docx'){
        $kv_texts = $this->kv_read_word('cv/'.$fileName);
        $client->avatar = $kv_texts;
        $client->save();
      }
      // sending back with message
      Session::flash('success', 'Upload successfully');
      return Redirect::action('ClientController@detect', array($client->id));
    }
    else {
      // sending back with error message.
      Session::flash('error', 'uploaded file is not valid');
      return Redirect::to('');
    }
    }

    public function histo(Request $request){
        $client = Client::findOrFail($request->get('id'));
        $input = $request->all();
        $client->update($input);

        Session::flash('flash_message', 'Task successfully added!');

        return redirect('client/'.$request->input('id'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $client = Client::findOrFail($id);
        $task = Task::orderBy('created_at', 'asc')->get();
        return view('client.show',compact('client','task'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $client = Client::findOrFail($id);   
        $cat = Category::all('name','id')->pluck('name', 'id');  
        return view('client.edit',compact('client','cat'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($request->input('id'));
        $input = $request->all();
        $client->update($input);

        Session::flash('flash_message', 'Task successfully added!');

        return redirect()->back();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       Client::find($id)->delete();
       return redirect('client');
    }


    function kv_read_word($input_file){
         $kv_strip_texts = '';
             $kv_texts = '';
        if(!$input_file || !file_exists($input_file)) return false;

        $zip = zip_open($input_file);

        if (!$zip || is_numeric($zip)) return false;


        while ($zip_entry = zip_read($zip)) {

            if (zip_entry_open($zip, $zip_entry) == FALSE) continue;

            if (zip_entry_name($zip_entry) != "word/document.xml") continue;

            $kv_texts .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

            zip_entry_close($zip_entry);
        }

        zip_close($zip);


        $kv_texts = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $kv_texts);
        $kv_texts = str_replace('</w:r></w:p>', "\r\n", $kv_texts);
        $kv_strip_texts = nl2br(strip_tags($kv_texts,''));

        return $kv_strip_texts;
    }

    public function detect($id){
        $client = Client::findOrFail($id);     
        $cat = Category::all('name','id')->pluck('name', 'id')  ; 
        return view('client.detect',compact('client','cat'));

    }

    public function addref(Request $request){
        $client = Client::findOrFail($request->input('id'));
        $input = $request->all();
        $client->update($input);

        Session::flash('flash_message', 'Task successfully added!');

        return redirect('client/'.$request->input('id').'/edit');
    }

}
