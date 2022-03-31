<?php

namespace App\Http\Controllers;

use App\Model\ads;
use App\Model\media;
use App\Model\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Image;
use Storage;
use Validator;
use JWTAuth;
use function foo\func;


class AdsController extends Controller
{

    public function __construct()
    {
        $this->middleware(['assign.guard:users', 'jwt.auth'], ['except' => [
            'store',
            'index',
            'show',
            'Slider',
            'StepViews']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $user = new User();
        //Get Count of Events Between User Last Seen & Events Created_At
        // if Send User in Headers
        $countObj = array();
        if (isset(auth()->user()->id)) {
            $userData = $user->where('id', auth()->user()->id)->first();
            $last_seen_ads = $userData->ads != '' ? $userData->ads : '';
            $ads = new ads();

            $count_ads = $ads;
            if ($last_seen_ads != ''):
                $count_ads = $count_ads->where('created_at', '>', $last_seen_ads);
            endif;
            $count_ads = $count_ads->where('country_id', request('country_id'));
            $count_ads = $count_ads->count();

            if (!$request->hasHeader('type') && $request->header('type') == '') {
                $update_last_seen = $user->where('id', auth()->user()->id)->update([
                    'ads' => date("Y-m-d H:i:s")
                ]);
            } else {
                $update_last_seen = $user->where('id', auth()->user()->id)->update([
                    'ads' => date("Y-m-d H:i:s")
                ]);
            }

        } else {
//            $count_ads = ads::where('country_id', request('country_id'))->count();
            $count_ads = 0;
        }
        $countObj = array(
            'count_ads' => $count_ads
        );


        if (isset($request['ads_category_id']))
            $output = ads::where('ads_category_id', $request['ads_category_id'])
                ->where('country_id', request('country_id'))
                ->where('state', 1)
                ->where('is_admin', null)
//                ->with(['media', 'adsImages'])
                ->orderBy('id', 'desc')
                ->orderBy('special', 'desc')
                ->orderBy('pin', 'desc')
                ->paginate(10000);
        else
            $output = ads::where('state', 1)
                ->where('country_id', request('country_id'))
                ->where('is_admin', null)
                ->with('media')
                ->orderBy('id', 'desc')
//                ->with(['media', 'adsImages'])
                ->orderBy('special', 'desc')
                ->orderBy('pin', 'desc')
                ->paginate(10000);


//        $output[] = $countObj;

        //set last seen into users table

        return $output;
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

    public function StepViews($id)
    {
        $getOld = ads::where('id', $id)->first()->views;
        ads::where('id', $id)->update(['views' => (integer)$getOld + 1]);
        return ['state' => 202];

    }

    public function Slider(Request $request)
    {
        if (isset($request['ads_category_id']))
            $output = ads::where('ads_category_id', $request['ads_category_id'])
                ->where('country_id', request('country_id'))
                ->where('state', 1)
                ->where('is_admin', 1)
                ->with(['media', 'adsImages'])
//                ->orderBy('special', 'desc')
                ->orderBy('pin', 'desc')
                ->paginate(10);
        else
            $output = ads::where('state', 1)
                ->where('country_id', request('country_id'))
                ->where('is_admin', 1)
                ->with('media')
//                ->orderBy('id', 'desc')
                ->with(['media', 'adsImages'])
//                ->orderBy('special', 'desc')
                ->orderBy('pin', 'desc')
                ->paginate(10);

//        $output[] = $countObj;

        //set last seen into users table

        return $output;

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        return auth()->user();
        $input = Request()->all();
//        $input['user_id'] = Auth::id();
        $input['user_id'] = 1;
        $rules = [
            'title' => 'required|String',
            'brief' => 'required|String',
            'image' => 'required|mimes:jpeg,jpg,png,gif|required',
            'ads_category_id' => 'required',
            'phone' => 'required'
        ];

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 401);
        }

        if ($request->has('website_url') && !empty($input['website_url']) && stripos(@$input['instagram_url'], "http://") === false && stripos(@$input['instagram_url'], "https://") === false) {
            return response()->json(['success' => false, 'error' => ['url must start with http:// or https://']], 401);

        }
        if ($request->has('website_url') && !empty($input['website_url']) && stripos(@$input['instagram_url'], "http://") === false && stripos(@$input['instagram_url'], "https://") === false) {
            return response()->json(['success' => false, 'error' => ['url must start with http:// or https://']], 401);

        }
        if ($request->has('website_url') && !empty($input['website_url']) && stripos(@$input['website_url'], "http://") === false && stripos(@$input['website_url'], "https://") === false) {
            return response()->json(['success' => false, 'error' => ['url must start with http:// or https://']], 401);

        }


        $image = $input['image'];
        $image_name = 'media-' . rand(10, 100) . date('mdYhis') . '.' . pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
        $image_path = 'ads/';
        $image = Image::make($image);
        $image->orientate();
        $image->resize(1024, null, function ($constraint) {
            $constraint->upsize();
            $constraint->aspectRatio();
        });
        $image->save($image_path . $image_name);

        $input['image'] = '/ads/' . $image_name;
        $ads = new ads();
        $ad = $ads::create($input);
        //insert media
        if (isset($input['media'])) {
            for ($i = 0; $i < count($input['media']); $i++) {
                $media = $input['media'][$i];
                $image_name = 'media-' . ($i + 30 * 35) . '-' . rand(100, 1000) . '-' . ($i * 30 + 95) . '.' . $media->getClientOriginalName();
                $thump_name = 'media-' . ($i + 30 * 35) . '-' . rand(100, 1000) . '-' . ($i * 30 + 95) . '.' . $media->getClientOriginalName();
                $thump_path = 'thump/';
                $image_path = 'ads/';

                $height = Image::make($media)->height();
                $newWidth = ($height * 8) / 5;
                $image = Image::make($media);
                $image->orientate();
                $image->resize(1024, null, function ($constraint) {
                    $constraint->upsize();
                    $constraint->aspectRatio();
                });
                $image->save($image_path . $image_name);

                $thump = Image::make($media);
                $thump->orientate();
                $thump->resize(1024, null, function ($constraint) {
                    $constraint->upsize();
                    $constraint->aspectRatio();
                });

                $thump->save($thump_path . $thump_name);
                $input['image'] = '/ads/' . $image_name;
                $input['thump'] = '/thump/' . $thump_name;
                $media = new media();
                $media->create(['ads_id' => $ad->id, 'image' => $input['image'], 'thump' => $input['thump']]);
            }

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
        $output = ads::where('id', $id)->first();
        $ads_image = media::where('ads_id', $id)->paginate(5)->toArray();

        $special_image[] = array(

            'id' => null,
            'event_id' => null,
            'ads_id' => $output->id,
            'image' => $output->image,
            'thump' => $output->image
        );
        if ($ads_image['current_page'] == '1'):
            array_splice($ads_image['data'], 0, 0, $special_image); // splice in at position 3
        endif;

        return $ads_image;
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
        $input['user_id'] = Auth::id();
        $rules = [
            'brief' => 'required|String',
            'ads_category_id' => 'required',
            'phone' => 'required',
            'user_id' => [
                'required',
                Rule::in([ads::where('id', $id)->value('user_id')]),
            ],
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 401);
        }
        if ($request->has('website_url') && !empty($input['website_url']) && stripos(@$input['instagram_url'], "http://") === false && stripos(@$input['instagram_url'], "https://") === false) {
            return response()->json(['success' => false, 'error' => ['url must start with http:// or https://']], 401);

        }
        if ($request->has('website_url') && !empty($input['website_url']) && stripos(@$input['instagram_url'], "http://") === false && stripos(@$input['instagram_url'], "https://") === false) {
            return response()->json(['success' => false, 'error' => ['url must start with http:// or https://']], 401);

        }
        if ($request->has('website_url') && !empty($input['website_url']) && stripos(@$input['website_url'], "http://") === false && stripos(@$input['website_url'], "https://") === false) {
            return response()->json(['success' => false, 'error' => ['url must start with http:// or https://']], 401);

        }
        if (isset($input['image'])) {
            $image = $input['image'];
            $image_name = 'media-' . rand(10, 100) . date('mdYhis') . '.' . pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
            $image_path = 'image/ads/';


            $photo = Image::make($image)
                ->fit(1600, 1400, function ($constraint) {
//                $constraint->aspectRatio();
                })->encode('jpg', 50);
            Storage::disk('public')->put($image_path . $image_name, $photo);
//
//            Storage::disk('public')->putFileAs($image_path, $image, $image_name);
            $input['image'] = '/storage/image/ads/' . $image_name;
            ads::where('id', $id)->update(['title' => $input['title'], 'brief' => $input['brief'], 'phone' => $input['phone'], 'image' => $input['image'], 'ads_category_id' => $input['ads_category_id']]);

        } else {
            ads::where('id', $id)->update($request->only('title', 'brief', 'phone', 'ads_category_id'));

        }
        //insert media
        if (isset($input['media'])) {
            media::where('ads_id', $id)->delete();

            for ($i = 0; $i < count($input['media']); $i++) {
                $media = $input['media'][$i];
                $media_name = 'media-' . rand(10, 100) . date('mdYhis') . '.' . pathinfo($media->getClientOriginalName(), PATHINFO_EXTENSION);
                $thump_name = 'media-' . ($i + 30) . rand(100, 1000) . $i . date('mdYhis') . '.' . $media->getClientOriginalName();
                $thump_path = 'thump/';
                $media_path = 'image/ads/';

                $height = Image::make($media)->height();
                $newWidth = ($height * 8) / 5;
                $thump = Image::make($media)
                    ->resize($newWidth, $height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode('jpg', 50);

                $photo = Image::make($media)
                    ->fit($newWidth, $height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode('jpg', 50);
                Storage::disk('public')->put($media_path . $media_name, $photo);

//                Storage::disk('public')->putFileAs($media_path, $media, $media_name);
                $thump->save($thump_path . $thump_name);
                $input['thump'] = '/thump/' . $thump_name;
                $input['image'] = '/storage/image/ads/' . $media_name;
                $media = new media();
                $media->create(['ads_id' => $id, 'image' => $input['image'], 'thump' => $input['thump']]);
            }

        }

        return ['state' => 202];
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Model\ads $ads
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $input = Request()->all();
        $input['user_id'] = Auth::id();
        $rules = [
            'user_id' => [
                'required',
                Rule::in([ads::where('id', $id)->value('user_id')]),
            ],
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 401);
        }
        ads::find($id)->delete();
        media::where('ads_id', $id)->delete();
        return ['state' => 202];
    }
}
