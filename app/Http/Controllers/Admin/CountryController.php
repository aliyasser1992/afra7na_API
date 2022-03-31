<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\country;
use App\Model\region;
use Validator,Image,Storage;
class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $input=Request()->all();
        if(isset($input['paginate']))
        return country::all();
        else
        return country::paginate(10);
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
            'currency_ar' => 'required',
            'currency_en' => 'required',
            'code' => 'required',
            'image' => 'required|mimes:jpeg,jpg,png,gif|max:10000'
       ];

       $validator = Validator::make($input, $rules);
       if($validator->fails()) {
           return response()->json(['success'=> false, 'error'=> $validator->messages()],401);
       }
       $image=$input['image'];
       $image_name = 'flag-'.rand(10,100) . date('mdYhis') . '.' . pathinfo($image->getClientOriginalName() , PATHINFO_EXTENSION);
       $image_path = 'image/event/';
//       $photo = Image::make($image)
//         ->resize(300, null ,function ($constraint) {
//           $constraint->aspectRatio();
//       })->encode('jpg',50);
//       Storage::disk('public')->put(  $image_path.$image_name, $photo);
        Storage::disk('public')->putFileAs($image_path, $image, $image_name);

       $input['image']='/storage/image/event/'.$image_name;
       $country = new country();
       $country->create($input);
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
            'currency_ar' => 'required',
            'currency_en' => 'required',
            'code' => 'required',
            'image' => 'mimes:jpeg,jpg,png,gif|max:10000',
       ];

       $validator = Validator::make($input, $rules);
       if($validator->fails()) {
           return response()->json(['success'=> false, 'error'=> $validator->messages()],401);
       }
       if(isset($input['image'])){
       $image=$input['image'];
       $image_name = 'flag-'.rand(10,100) . date('mdYhis') . '.' . pathinfo($image->getClientOriginalName() , PATHINFO_EXTENSION);
       $image_path = 'image/event/';

//       $photo = Image::make($image)
//         ->resize(300, null ,function ($constraint) {
//           $constraint->aspectRatio();
//       })->encode('jpg',50);
//       Storage::disk('public')->put(  $image_path.$image_name, $photo);
           Storage::disk('public')->putFileAs($image_path, $image, $image_name);

           $input['image']='/storage/image/event/'.$image_name;
       }
       country::where('id',$id)->update($input);
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
        region::where('country_id',$id)->delete();
        country::where('id',$id)->delete();
        return ['state'=>202];
    }
}
