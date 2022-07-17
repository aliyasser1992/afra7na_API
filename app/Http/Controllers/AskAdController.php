<?php

namespace App\Http\Controllers;

use App\Model\ask_ad;
use Illuminate\Http\Request;
use Validator;
class AskAdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'name' => 'required|String',
            'email' => 'required|String',  
            'phone' => 'required',
            'brief' => 'required',
           
       ];
     
       $validator = Validator::make($input, $rules);
       if($validator->fails()) {
           return response()->json(['success'=> false, 'error'=> $validator->messages()],400);
       }
       $ask_ad = new ask_ad();
       $ask_ad->create($input);
       return ['state'=>202];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\ask_ad  $ask_ad
     * @return \Illuminate\Http\Response
     */
    public function show(ask_ad $ask_ad)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\ask_ad  $ask_ad
     * @return \Illuminate\Http\Response
     */
    public function edit(ask_ad $ask_ad)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\ask_ad  $ask_ad
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ask_ad $ask_ad)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\ask_ad  $ask_ad
     * @return \Illuminate\Http\Response
     */
    public function destroy(ask_ad $ask_ad)
    {
        //
    }
}
