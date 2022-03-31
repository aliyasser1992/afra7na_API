<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\ads;
use App\Model\AdsImages;
use App\Model\media;
use Illuminate\Http\Request;
use Image;
use Storage;
use Validator;

class AdsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(AdsImages $AdsImages, media $media)
    {
        $this->ads_images = $AdsImages;
        $this->media = $media;
    }

    public function index()
    {
        $input = Request()->all();
        if (isset($input['is_admin']) && isset($input['ads_category_id'])):
            return ads::where('ads_category_id', $input['ads_category_id'])->where('is_admin', $input['is_admin'])->with(['ads_category', 'country', 'media'])->paginate(10);
        elseif (isset($input['ads_category_id'])):
            return ads::where('ads_category_id', $input['ads_category_id'])->with(['ads_category', 'country', 'media'])->paginate(10);
        elseif (isset($input['is_admin'])):
            return ads::where('is_admin', $input['is_admin'])->with(['ads_category', 'country', 'media'])->paginate(10);
        else:
            return ads::with(['ads_category', 'country', 'media'])->paginate(10);
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

    public function Ads_State($id, $state)
    {
        $state == 1 ? $state = 0 : $state = 1;

        if ($state === 1) {
            ads::where('id', $id)->update([
                'state' => $state,
            ]);
        } else {
            ads::where('id', $id)->update([
                'state' => $state,
                'special' => 0,
                'from' => null,
                "to" => null
            ]);
        }

        return ['state' => 'success', 'data' => $state];
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
            'title' => 'required|String', // address
            'country_id' => 'required',
            'brief' => 'required|String',
            'phone' => 'required'

        ];
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 401);
        }
        $ads = new ads();
        if ($request->has('special') && $input['special'] != '') {
            $input['special'] = $input['special'] == 'true' ? 1 : 0;
        }

        $image = $input['images'][0];
        $image_name = 'media-' . rand(10, 100) . date('mdYhis') . '.' . pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
        $image_path = 'ads/';
//        Storage::disk('public')->putFileAs($image_path, $image, $image_name);


//        $image_name = 'media-' . rand(10, 100) . date('mdYhis') . '.' . pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
//        $image_path = 'image/ads/';
        $image = Image::make($image);
        $image->orientate();
        $image->resize(1024, null, function ($constraint) {
            $constraint->upsize();
            $constraint->aspectRatio();
        });
        $image->save($image_path . $image_name);
        $input['image'] = '/ads/' . $image_name;

//        $input['is_admin'] = 1;
        $input['state'] = 1;
        $output = $ads::create($input);
        $ads_id = $output->id;

//        $images = json_decode($input['images']);

        foreach ($input['images'] as $key => $image) {
            $image_name = rand(10, 100) . date('mdYhis') . '.' . $image->getClientOriginalName();
            $thump_name = 'media-' . rand(10, 100) . date('mdYhis') . '.' . pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
            $image_path = 'ads/';
            $thump_path = 'thump/';

            $image = Image::make($image);
            $image->orientate();
            $image->resize(1024, null, function ($constraint) {
                $constraint->upsize();
                $constraint->aspectRatio();
            });
            $image->save($image_path . $image_name);

            $height = Image::make($image)->height();
            $newWidth = ($height * 8) / 5;

            $thump = Image::make($image);
            $thump->orientate();
            $thump->resize(1024, null, function ($constraint) {
                $constraint->upsize();
                $constraint->aspectRatio();
            });
            $thump->save($thump_path . $thump_name);
            $input['thump'] = '/thump/' . $thump_name;
            $input['image'] = '/ads/' . $image_name;
            $input['ads_id'] = $ads_id;
            $this->media->create($input);
        }

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
//        $rules = [
//            'title_ar' => 'required|String',
//            'title_en' => 'required|String',
//            'image' => 'mimes:jpeg,jpg,png,gif|max:10000',
//            'ads_category_id' => 'required',
//        ];
//
//        $validator = Validator::make($input, $rules);
//        if ($validator->fails()) {
//            return response()->json(['success' => false, 'error' => $validator->messages()], 401);
//        }
//        if (isset($input['image'])) {
//            $image = $input['image'];
//            $image_name = 'media-' . rand(10, 100) . date('mdYhis') . '.' . pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
//            $image_path = 'image/ads/';
//
//            Storage::disk('public')->putFileAs($image_path, $image, $image_name);
//            $input['image'] = '/storage/image/ads/' . $image_name;
//        }

        if ($request->has('special') && $input['special'] != '') {
            $input['special'] = $input['special'] == 'true' ? 1 : 0;
            ads::where('id', $id)->update([
                "title" => $input['title'],
                "phone" => $input['phone'],
                "brief" => $input['brief'],
                "special" => $input['special'],
                "from" => $input['from'],
                "to" => $input['to'],
                "instagram_url" => @$input['instagram_url'],
                "twitter_url" => @$input['twitter_url'],
                "snap_chat_url" => @$input['snap_chat_url'],
                "whatsapp_number" => @$input['whatsapp_number'],
                "website_url" => @$input['website_url'],
                "is_admin" => @$input['is_admin'],
//                "link" => @$input['link'],
                "ads_category_id" => $input['ads_category_id'],
                "country_id" => $input['country_id'],
            ]);
        } else {
            ads::where('id', $id)->update([
                "title" => $input['title'],
                "phone" => $input['phone'],
                "brief" => $input['brief'],
                "instagram_url" => @$input['instagram_url'],
                "twitter_url" => @$input['twitter_url'],
                "snap_chat_url" => @$input['snap_chat_url'],
                "whatsapp_number" => @$input['whatsapp_number'],
                "website_url" => @$input['website_url'],
                "is_admin" => @$input['is_admin'],

//                "link" => @$input['link'],
                "ads_category_id" => $input['ads_category_id'],
                "country_id" => $input['country_id'],
            ]);
        }


        if ($request->has('images') && count($input['images']) > 0) {
            foreach ($input['images'] as $key => $image) {
//                $image_name = rand(10, 100) . date('mdYhis') . '.' . $image->getClientOriginalName();
//                $image_path = 'image/ads/';
//                Storage::disk('public')->putFileAs($image_path, $image, $image_name);
                $image_name = 'media-' . rand(10, 100) . date('mdYhis') . '.' . pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
                $image_path = 'image/ads/';
                $photo = Image::make($image)
                    ->fit(1600, 1400, function ($constraint) {
//            $constraint->aspectRatio();
//                $constraint->upsize();
                    })->encode('jpg', 50);
                Storage::disk('public')->put($image_path . $image_name, $photo);
                $input['image'] = '/storage/image/ads/' . $image_name;
                $input['ads_id'] = $id;


                $thump_name = 'media-' . rand(10, 100) . date('mdYhis') . '.' . pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
                $thump_path = 'thump/';

                $height = Image::make($image)->height();
                $newWidth = ($height * 8) / 5;
                $thump = Image::make($image)
                    ->resize($newWidth, $height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode('jpg', 50);
                $thump->save($thump_path . $thump_name);

                $input['thump'] = '/thump/' . $thump_name;


                $this->media->create($input);
            }
        }

        return ['state' => 202];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ads::where('id', $id)->delete();
        $this->ads_images->where('ads_id', $id)->delete();
        return ['state' => 202];
    }

    public function DeleteAdsImage($id)
    {
        $this->ads_images->where('id', $id)->delete();
        return ['state' => 202];
    }

    public function FakeViews($id)
    {
        $oldnumer = ads::where('id', $id)->first()->views;
        ads::where('id', $id)->update(['views' => (integer)$oldnumer + 100]);
        return ['state' => 202];
    }

    public function Pain($id)
    {
        $old_state = ads::where('id', $id)->first()->pin;
        ads::where('id', $id)->update(['pin' => $old_state == 1 ? 0 : 1]);
        return ['state' => 202, 'data' => $old_state == 1 ? 0 : 1];
    }
}
