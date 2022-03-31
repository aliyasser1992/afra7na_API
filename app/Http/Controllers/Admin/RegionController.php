<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\region;
use Validator,Storage;
class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $input = Request()->all();
        if(isset($input['country_id']))
        return region::where('country_id',$input['country_id'])->with('country')->paginate(10);
        else
        return region::with('country')->paginate(10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input=Request()->all();
        $rules = [
            'title_ar' => 'required|String',
            'title_en' => 'required|String',
            'country_id' => 'required',
       ];
     
       $validator = Validator::make($input, $rules);
       if($validator->fails()) {
           return response()->json(['success'=> false, 'error'=> $validator->messages()],401);
       }
       $region = new region();
       $region->create($input);
       return ['state'=>202]; 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $input=Request()->all();
        $rules = [
            'title_ar' => 'required|String',
            'title_en' => 'required|String',
            'country_id' => 'required',
       ];
     
       $validator = Validator::make($input, $rules);
       if($validator->fails()) {
           return response()->json(['success'=> false, 'error'=> $validator->messages()],401);
       }   
       region::where('id',$id)->update($input);
       return ['state'=>202]; 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        region::where('id',$id)->delete();   
        return ['state'=>202];
    }
}
