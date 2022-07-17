<?php

namespace App\Http\Controllers;

use App\Model\ask_special_event;
use Illuminate\Http\Request;
use Validator;
class AskSpecialEventController extends Controller
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
            'event_id' => 'required',
            'start_time' => 'required|date_format:Y-m-d H:i:s',
       ];
     
       $validator = Validator::make($input, $rules);
       if($validator->fails()) {
           return response()->json(['success'=> false, 'error'=> $validator->messages()],400);
       }
       $ask_special_event = new ask_special_event();
       $ask_special_event->create($input);
       return ['state'=>202];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\ask_special_event  $ask_special_event
     * @return \Illuminate\Http\Response
     */
    public function show(ask_special_event $ask_special_event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\ask_special_event  $ask_special_event
     * @return \Illuminate\Http\Response
     */
    public function edit(ask_special_event $ask_special_event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\ask_special_event  $ask_special_event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ask_special_event $ask_special_event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\ask_special_event  $ask_special_event
     * @return \Illuminate\Http\Response
     */
    public function destroy(ask_special_event $ask_special_event)
    {
        //
    }
}
