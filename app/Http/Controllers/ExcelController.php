<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Item;
use Excel;

class ExcelController extends Controller
{



    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('excel');

    }

	/**
     * Users File Export
     *
     */
	public function exportUsers() {

		$users = User::select('id', 'name', 'email', 'created_at')->get();

		$data = Excel::create('users', function($excel) use($users) {
		    $excel->sheet('Sheet 1', function($sheet) use($users) {
		        $sheet->fromArray($users);
		    });
		});

		$data->export('xls');

	}

	/**
     * File Export
     *
     * @var array
     */
	public function downloadExcel(Request $request, $type)
	{
		$data = Item::get()->toArray();
		return Excel::create('itsolutionstuff_example', function($excel) use ($data) {
			$excel->sheet('mySheet', function($sheet) use ($data)
	        {
				$sheet->fromArray($data);
	        });
		})->download($type);
	}

	/**
     * Import file into database
     *
     * @var array
     */
	public function importExcel(Request $request)
	{

		if($request->hasFile('import_file')){
			$path = $request->file('import_file')->getRealPath();

			$data = Excel::load($path, function($reader) {})->get();

			if(!empty($data) && $data->count()){

				foreach ($data->toArray() as $key => $value) {
					if(!empty($value)){
						foreach ($value as $v) {
							$insert[] = ['title' => $v['title'], 'description' => $v['description']];
						}
					}
				}


				if(!empty($insert)){
					Item::insert($insert);
					return back()->with('success','Insert Record successfully.');
				}

			}

		}

		return back()->with('error','Please Check your file, Something is wrong there.');
	}


}
