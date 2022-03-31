<?php

namespace App\Http\Controllers;

use App\Model\support;
use Illuminate\Http\Request;
use Validator;
class SupportController extends Controller
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
            'message' => 'required',
           
       ];
     
       $validator = Validator::make($input, $rules);
       if($validator->fails()) {
           return response()->json(['success'=> false, 'error'=> $validator->messages()],400);
       }
       $support = new support();
       $support->create($input);
       return ['state'=>202];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\support  $support
     * @return \Illuminate\Http\Response
     */
    public function show(support $support)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\support  $support
     * @return \Illuminate\Http\Response
     */
    public function edit(support $support)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\support  $support
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, support $support)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\support  $support
     * @return \Illuminate\Http\Response
     */
    public function destroy(support $support)
    {
        //
    }
}
