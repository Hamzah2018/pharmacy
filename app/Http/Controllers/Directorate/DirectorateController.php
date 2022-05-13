<?php

namespace App\Http\Controllers\Directorate;

use App\Http\Controllers\Controller;
use App\Models\Directorate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class DirectorateController extends Controller
{
    public function index()
    {
        //
        $directorates = Directorate::get();
        return response($directorates);
        // return view('directorates', compact('directorates'));
    }

    public function store(Request $request)
    {
      $validator = Validator::make($request->all(), [
        // min:2 becouse some cities in Yemen can be two litter like Ab
        'name' => 'required|min:2|max:30|string',
        'city_id' => 'required',
      ]);
      Directorate::create([
        'name' => $request->input('name'),
        'city_id' => $request->input('city_id'),
      ]);

      return response(['added successfully', $validator->errors()]);
    //  return redirect()->back()->with('status', 'added successfully');
    }

    public function show($id)
    {
      $directorates = Directorate::where('id', $id)->get();
      return response($directorates);

    }

    public function update()
    {
        //

    }

    public function destroy($id)
    {
      //
    }
}
