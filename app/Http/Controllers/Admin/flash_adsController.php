<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\flash_ads;
use Illuminate\Http\Request;
use Image;
use Storage;
use Validator;

class flash_adsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->has('country_id')):
            return flash_ads::with('Country')->where('country_id',request('country_id'))->paginate(10);
        else:
            return flash_ads::with('Country')->paginate(10);
        endif;
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = Request()->all();
        $rules = [
            'title_ar' => 'required|String',
            'title_en' => 'required|String',
            'image' => 'required|mimes:jpeg,jpg,png,gif|required|max:10000',
            'flag' => 'required',
            'country_id' => 'required'
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 401);
        }
        $image = $input['image'];
        $image_name = 'media-' . rand(10, 100) . date('mdYhis') . '.' . pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
        $image_path = 'image/flash_ads/';
//        $photo = Image::make($image)
//          ->resize(300, null ,function ($constraint) {
//            $constraint->aspectRatio();
//        })->encode('jpg',50);
//        Storage::disk('public')->put(  $image_path.$image_name, $photo);
//
        Storage::disk('public')->putFileAs($image_path, $image, $image_name);

        $input['image'] = '/storage/image/flash_ads/' . $image_name;
        $flash_ads = new flash_ads();
        flash_ads::create($input);
        return ['state' => 202];
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = Request()->all();
        $rules = [
            'title_ar' => 'required|String',
            'title_en' => 'required|String',
            'image' => 'mimes:jpeg,jpg,png,gif|max:10000',
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 401);
        }
        if (isset($input['image'])) {
            $image = $input['image'];
            $image_name = 'media-' . rand(10, 100) . date('mdYhis') . '.' . pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
            $image_path = 'image/flash_ads/';
//        $photo = Image::make($image)
//          ->resize(300, null ,function ($constraint) {
//            $constraint->aspectRatio();
//        })->encode('jpg',50);
//        Storage::disk('public')->put(  $image_path.$image_name, $photo);
            Storage::disk('public')->putFileAs($image_path, $image, $image_name);

            $input['image'] = '/storage/image/flash_ads/' . $image_name;
        }
        flash_ads::where('id', $id)->update($input);
        return ['state' => 202];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function AddViews($id)
    {
        $input = Request()->all();
        $views = flash_ads::find($id)->view;
        flash_ads::find($id)->update(['view' => (integer)$views + 100]);
        return ['state' => 202];
    }

    public function destroy($id)
    {
        flash_ads::where('id', $id)->delete();
        return ['state' => 202];
    }
}
