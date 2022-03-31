<?php

namespace App\Http\Controllers\Admin;

use App\Filters\eventsFilter;
use App\Filters\usersFilter;
use App\Http\Controllers\Controller;
use App\Model\event;
use App\Model\notification;
use App\Model\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Image;
use OneSignal;
use Storage;
use Validator;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(eventsFilter $filters)
    {
        return event::filter($filters)->with('country', 'region', 'user', 'media')->withTrashed()->paginate(10);
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


    public function sendNoti($id, usersFilter $filters)
    {
        $input = Request()->all();
        $event = event::where('id', $id)->first();
        $users = User::filter($filters)->pluck('id');
        if ($event['invitation_start_time'] < Carbon::now()) {
            return Response()->json(['error' => array('event' => ['invitation start time is in past'])], 400);
        } else {
            for ($i = 0; $i < count($users); $i++) {
                $notification = new notification();
                $notification::create(['title_ar' => 'دعوة فرح', 'description_ar' => $event['title'] . "سوف يتم ابتداء الحدث", 'title_en' => 'wedding invetation', 'description_ar' => $event['title'] . "event will start",
                    'description_en' => $event['title'] . "event will start", 'event_id' => $event['id'], 'user_id' => $users[$i]]);
                OneSignal::sendNotificationUsingTags(
                    $event['title'] . "سوف يتم ابتداء الحدث",
                    array(
                        ["field" => "tag", "key" => "userId", "relation" => "=", "value" => $users[$i]]
                    ),
                    $url = null,
                    $data = null,
                    $buttons = null,
                    $schedule = $event['invitation_start_time']
                );
            }
            return ['state' => 202];
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    //destroy event

    public function remove_add_event($id)
    {

        event::where('id', $id)->update([
            'ad_image' => null,
            'ad_image_sort' => null
        ]);
        return ['state' => 202];
    }

    public function event_ad_image(Request $request, $id)
    {


        $input = Request()->all();
        $rules = [
            'ad_image_sort' => 'required|integer',
//            'ad_image' => 'mimes:jpeg,jpg,png,gif|max:10000'
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 401);
        }
        if ($request->has('ad_image')) {
            $image = $input['ad_image'];
            $image_name = 'media-' . rand(10, 100) . date('mdYhis') . '.' . pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
            $image_path = 'image/event/';
            Storage::disk('public')->putFileAs($image_path, $image, $image_name);
            $input['ad_image'] = '/storage/image/event/' . $image_name;

            $thump_name = 'media-' . rand(10, 100) . date('mdYhis') . '.' . pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
            $height = Image::make($image)->height();
            $newWidth = ($height * 8) / 5;
            $thump = Image::make($image)
                ->resize($newWidth, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('jpg', 50);
            $thump_path = 'thump/';
            $thump->save($thump_path . $thump_name);
            $input['thump'] = '/thump/' . $thump_name;


            $output = event::where('id', $id)->update([
                'ad_image' => $input['ad_image'],
                'ad_image_sort' => $input['ad_image_sort'],
                'ads_link' => @$input['ads_link'],
                'ad_image_thump'=> $input['thump']
            ]);


        }else{
            $output = event::where('id', $id)->update([
                'ad_image_sort' => $input['ad_image_sort'],
                'ads_link' => @$input['ads_link']
            ]);
        }

        return ['state' => 202];
    }

    public function SpecialEvent($id)
    {
        $input = Request()->all();
        $input['special'] = $input['special'] == 'true' ? 1 : 0;
        event::where('id', $id)->update($input);
        return ['state' => 202, 'data' => $input['special']];

    }


    public function destroy($id)
    {
        event::where('id', $id)->forceDelete();
        return Response()->json(['event' => 'deleted'], 200);
    }

    // soft delete event
    public function trached($id)
    {
        event::where('id', $id)->delete();
        return Response()->json(['event' => 'trached'], 200);
    }

    // restore event
    public function cancel_trached($id)
    {
        event::where('id', $id)->restore();
        event::where('id',$id)->update([
            'created_at' => date('Y-m-d H:i:s')
        ]);
        return Response()->json(['event' => 'cancel_trached'], 200);
    }
}
