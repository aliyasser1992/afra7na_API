<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\ads_category;
use Validator,Image,Storage;

class AdsCategoryController extends Controller
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
        return ads_category::all();
        else
        return ads_category::paginate(10);
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
            'image' => 'required|mimes:jpeg,jpg,png,gif|required|max:10000',
       ];

       $validator = Validator::make($input, $rules);
       if($validator->fails()) {
           return response()->json(['success'=> false, 'error'=> $validator->messages()],401);
       }
       $image=$input['image'];
        $image_name = 'media-'.rand(10,100) . date('mdYhis') . '.' . pathinfo($image->getClientOriginalName() , PATHINFO_EXTENSION);
        $image_path = 'image/ads_category/';
//        $photo = Image::make($image);
//          ->resize(300, null ,function ($constraint) {
//            $constraint->aspectRatio();
//        })->encode('jpg',50);
//        Storage::disk('public')->put(  $image_path.$image_name, $photo);
        Storage::disk('public')->putFileAs($image_path, $image, $image_name);

        $input['image']='/storage/image/ads_category/'.$image_name;
        $ads_category = new ads_category();
        $ads_category::create($input);
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
            'image' => 'mimes:jpeg,jpg,png,gif|max:10000',
       ];

       $validator = Validator::make($input, $rules);
       if($validator->fails()) {
           return response()->json(['success'=> false, 'error'=> $validator->messages()],401);
       }
       if(isset($input['image'])){
        $image=$input['image'];
        $image_name = 'media-'.rand(10,100) . date('mdYhis') . '.' . pathinfo($image->getClientOriginalName() , PATHINFO_EXTENSION);
        $image_path = 'image/ads_category/';
//        $photo = Image::make($image)
//          ->resize(300, null ,function ($constraint) {
//            $constraint->aspectRatio();
//        })->encode('jpg',50);
//        Storage::disk('public')->put(  $image_path.$image_name, $photo);
           Storage::disk('public')->putFileAs($image_path, $image, $image_name);
           $input['image']='/storage/image/ads_category/'.$image_name;
        }
        ads_category::where('id',$id)->update($input);
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
        ads_category::where('id',$id)->delete();
        return ['state'=>202];
    }
}
