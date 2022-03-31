<?php

namespace App\Http\Controllers;

use App\Model\favourite;
use Illuminate\Http\Request;
use App\Model\event;
use Validator,Auth;
class FavouriteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return event::whereHas('favourite', function($q){
                            $q->where('user_id', Auth::id());
                        })->with('user','media','country','region')->paginate(10);
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
                'event_id'=>'required',  
            ];
          
            $validator = Validator::make($input, $rules);
            if($validator->fails()) {
                return response()->json(['success'=> false, 'error'=> $validator->messages()],401);
            }
            $favourite=favourite::where('favourite_id',$input['event_id'])->where('user_id',Auth::id())->first();
            switch (true) {
                case $favourite == null:
                      $favourite=favourite::firstOrNew(['favourite_id'=>$input['event_id'],'favourite_type'=>'App\Model\event',
                      'user_id'=>Auth::id()]);
                       $favourite->save();
                    break;
                case $favourite != null:
                      $favourite->delete();
                    break;
                default:
                      return Response()->json(['you enter unvalid modelType']);
                    
            }   
           
            return ['state'=>202];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\favourite  $favourite
     * @return \Illuminate\Http\Response
     */
    public function show(favourite $favourite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\favourite  $favourite
     * @return \Illuminate\Http\Response
     */
    public function edit(favourite $favourite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\favourite  $favourite
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, favourite $favourite)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\favourite  $favourite
     * @return \Illuminate\Http\Response
     */
    public function destroy(favourite $favourite)
    {
        //
    }
}
