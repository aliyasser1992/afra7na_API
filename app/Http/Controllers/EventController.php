<?php

namespace App\Http\Controllers;

use App\Filters\eventsFilter;
use App\Model\event;
use App\Model\media;
use App\Model\User;
use App\Model\UserEvents;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Image;
use Storage;
use Validator;
use Str;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function CreateViewINUsersEvent($country_id, $region_id, $category)
    {
//        return $category;
        $user_events = new UserEvents();

        $insertIntoUserEvent = $user_events->create([
            'user_id' => auth()->user()->id,
            'country_id' => $country_id,
            'region_id' => $region_id,
            'wedding' => $category == 1 ? 1 : 0,
            'occasions' => $category == 2 ? 1 : 0,
            'invitations' => $category == 3 ? 1 : 0
        ]);
    }

// if first time enter this regions or 48 hr
    private function GetLastUserLastSeen($category)
    {
        $user_events = new UserEvents();
//                    DB::enableQueryLog();
        $userData = $user_events
            ->select(DB::raw('id,user_id,country_id,region_id,max(created_at) as created_at '))
            ->where('user_id', auth()->user()->id)
            ->where('region_id', request('region_id'));
        if ($category == 1):
            $userData = $userData->where('wedding', '1');
        elseif ($category == 2):
            $userData = $userData->where('occasions', '1');
        elseif ($category == 3):
            $userData = $userData->where('invitations', '1');
        endif;
        $userData = $userData
            ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . " -48 hours")))
            ->where('created_at', '<=', date('Y-m-d H:i:s'))
            ->groupBy('region_id')
            ->first();
//            dd(DB::getQuerylog());


        return $userData;
    }

    private function GetEventsCountIfLastSeenFound($lastSeen, $region_id)
    {
//        return $lastSeen .': ' .$region_id;

        $count_events = event::select(DB::raw('count(region_id) as event_count, region_id'))
            ->where('region_id', $region_id)
            ->where('created_at', '>=', $lastSeen)
            ->first();
        return $count_events;
    }

    private function GetEventsCountBetweenTodayAnd48HoursAgo($region_id)
    {
        $count_events = event::select(DB::raw('count(region_id) as event_count, region_id'))
            ->where('region_id', $region_id)
            ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . " -48 hours")))
            ->where('created_at', '<=', date('Y-m-d H:i:s'))
//            ->where('deleted_at'  ,null)
//            ->withNoTrashed()
            ->first();
        return $count_events;
    }


    public function index(eventsFilter $filters, Request $request)
    {

        $user = new User();
        //Get Count of Events Between User Last Seen & Events Created_At
        // if Send User in Headers
        $countObj = array();
//
        if (isset(auth()->user()->id)) {
//            return  auth()->user();
            //set last seen into users table
            if (request('category') == '1') {
                $this->CreateViewINUsersEvent(request('country_id'), request('region_id'), 1);
            } else if (request('category') == '2') {
                $this->CreateViewINUsersEvent(request('country_id'), request('region_id'), 2);
            } else if (request('category') == '3') {
                $this->CreateViewINUsersEvent(request('country_id'), request('region_id'), 3);
            }

            $last_seen_wedding = $this->GetLastUserLastSeen(1) != null ? $this->GetLastUserLastSeen(1)->created_at->format('Y-m-d H:i:s') : '';
            $last_seen_occasions = $this->GetLastUserLastSeen(2) != null ? $this->GetLastUserLastSeen(2)->created_at->format('Y-m-d H:i:s') : '';
            $last_seen_invitations = $this->GetLastUserLastSeen(3) != null ? $this->GetLastUserLastSeen(3)->created_at->format('Y-m-d H:i:s') : '';

            $count_weeding = event::where('main_category_id', '1');
            if ($last_seen_wedding != '') :
                $count_weeding = $count_weeding->where('created_at', '>=', $last_seen_wedding);
            else:
                $count_weeding = $count_weeding
                    ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . " -48 hours")))
                    ->where('created_at', '<=', date('Y-m-d H:i:s'));
            endif;
            $count_weeding = $count_weeding
                ->where('country_id', request('country_id'))
                ->where('region_id', request('region_id'));
            $count_weeding = $count_weeding->count();
//return $last_seen_occasions;
//                                DB::enableQueryLog();

            $count_occasions = event::where('main_category_id', '2');
            if ($last_seen_occasions != ''):
                $count_occasions = $count_occasions->where('created_at', '>=', $last_seen_occasions);
            else:
                $count_occasions = $count_occasions
                    ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . " -48 hours")))
                    ->where('created_at', '<=', date('Y-m-d H:i:s'));
            endif;
            $count_occasions = $count_occasions
                ->where('country_id', request('country_id'))
                ->where('region_id', request('region_id'));
            $count_occasions = $count_occasions->count();
//                         dd(DB::getQuerylog());

//return $count_occasions;
            $count_invitations = event::where('main_category_id', '3');
            if ($last_seen_invitations != ''):
                $count_invitations = $count_invitations->where('created_at', '>=', $last_seen_invitations);
            else:
                $count_invitations = $count_invitations
                    ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . " -48 hours")))
                    ->where('created_at', '<=', date('Y-m-d H:i:s'));
            endif;
            $count_invitations = $count_invitations
                ->where('country_id', request('country_id'))
                ->where('region_id', request('region_id'));
            $count_invitations = $count_invitations->count();

        } else {
            $count_weeding = 0;
            $count_occasions = 0;
            $count_invitations = 0;
        }


        $countObj = array(
            'count_weeding' => $count_weeding,
            'count_occasions' => $count_occasions,
            'count_invitations' => $count_invitations
        );
//        return $countObj;
        $output = event
            ::filter($filters)
            ->with(['user', 'country', 'region'])
            ->orderBy('special', 'Desc')
            ->orderBy('id', 'Desc')
            ->paginate(10000);
        $output[] = $countObj;


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

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = Request()->all();

//        return         print_r($input['media']);


        $input['invitation_start_time'] = Carbon::now()->format('Y-m-d H:i:s');
        $rules = [
            'title' => 'required|String',
            'description' => 'required|String',
            'special_image' => 'required|mimes:jpeg,jpg,png,gif|required',
            'video' => 'mimes:mp4,mov,ogg,qt',
            'main_category_id' => 'required|Integer',
            // 'address' => 'required',
            // 'phone' => 'required',
            'user_id' => 'required|Integer',
            'invitation_start_time' => 'date_format:Y-m-d H:i:s',
            'media' => 'array|max:50'
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 401);
        }
        //insert normal image
        $image = $input['special_image'];
        $image_name = 'media-' . rand(10, 100) . date('mdYhis') . '.' . pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
        $image_path = 'event/';


        $image = Image::make($image);
//            ->resize('1000', '800', function ($constraint) {
////                $constraint->aspectRatio();
//                $constraint->upsize();
//            })
//            ->encode('jpg', 50);
        $image->orientate();
        $image->resize(1024, null, function ($constraint) {
            $constraint->upsize();
            $constraint->aspectRatio();
        });
//        Storage::disk('public')->put($image_path . $image_name, $image);
//        Storage::disk('public')->putFileAs($image_path, $image, $image_name);
        $image->save($image_path . $image_name);

        $input['special_image'] = '/event/' . $image_name;

        $event = event::create($input);
        //insert media
//        print_r($input['media']);
//        return;
        $event->delete();

        if (isset($input['media'])) {
            for ($i = 0; $i < count($input['media']); $i++) {
                $media = $input['media'][$i];
                $media_name = 'media-' . ($i + 30 * 35) . '-' . rand(100, 1000) . '-' . ($i * 30 + 95) . '.' . $media->getClientOriginalName();
                $thump_name = 'thump-' . ($i + 30 * 35) . '-' . rand(100, 1000) . '-' . ($i * 30 + 95) . '.' . $media->getClientOriginalName();
                $thump_path = 'thump/';
                $media_path = 'event/';

                $height = Image::make($media)->height();
                $newWidth = ($height * 8) / 5;
                $thump = Image::make($media);
//                    ->resize('1000', '800', function ($constraint) {
//                        $constraint->aspectRatio();
//                        $constraint->upsize();
//                    })
//                    ->encode('jpg', 50);
                $thump->orientate();
                $thump->resize(1024, null, function ($constraint) {
                    $constraint->upsize();
                    $constraint->aspectRatio();
                });

                $photo = Image::make($media);

//                    ->resize('1000', '800', function ($constraint) {
//                        $constraint->aspectRatio();
//                        $constraint->upsize();
//                    })
//                    ->encode('jpg', 50);

                $photo->orientate();
                $photo->resize(1024, null, function ($constraint) {
                    $constraint->upsize();
                    $constraint->aspectRatio();
                });
//                Storage::disk('public')->put($media_path . $media_name, $photo);
                $thump->save($thump_path . $thump_name);
                $photo->save($media_path . $media_name);
//                Storage::disk('public')->putFileAs($media_path, $media, $media_name);

                $input['image'] = '/event/' . $media_name;
                $input['thump'] = '/thump/' . $thump_name;
                $media = new media();
                $media->create(['event_id' => $event->id, 'image' => $input['image'], 'thump' => $input['thump']]);
            }


        }
        return ['state' => 202];


    }

    /**
     * Display the specified resource.
     *
     * @param \App\Model\event $event
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

//        $getViewerCount = event::find($id)->viewer;
//        event::where('id',$id)->update(['viewer' => $getViewerCount + 1]);

        $output = event::where('id', $id)->first();
        $sort = 0;
        if ($output->ad_image != null) {

            $sort = $output->ad_image_sort;
            $data[] = array(
                'id' => null,
                'event_id' => $output->id,
                'ads_id' => null,
                'ad_link' => $output->ads_link,
                'image' => $output->ad_image,
                'thump' => $output->ad_image_thump
            );
            $event_image = media::where('event_id', $id)->get()->toArray();
            array_splice($event_image, $sort - 1, 0, $data); // splice in at position 3
            $output = event::where('id', $id)->with(['user', 'country', 'region'])->first();

            $output->media = $event_image;
            return $output;
        } else {

            return $output = event::where('id', $id)->with(['media', 'user', 'country', 'region'])->first();
        }

    }


    public function EventMedia($event_id)
    {
        $output = event::where('id', $event_id)->first();
        $sort = 0;

        $event_image = media::where('event_id', $event_id)
            ->paginate(5)
//                ->get()
            ->toArray();

        $special_image[] = array(
            'id' => null,
            'event_id' => $output->id,
            'ads_id' => null,
            'image' => $output->special_image,
            'thump' => $output->special_image
        );
        if ($event_image['current_page'] == '1'):
            array_splice($event_image['data'], 0, 0, $special_image); // splice in at position 3
        endif;


        if ($output->ad_image != null) {

            $sort = $output->ad_image_sort;
            $data[] = array(
                'id' => null,
                'event_id' => $output->id,
                'ads_id' => null,
                'ad_link' => $output->ads_link,
                'image' => $output->ad_image,
                'thump' => $output->ad_image_thump
            );

            $newSort = $sort - 1; // 66 - 1 = 65
            // take 5
            $newSort = str_split($newSort);

//            return $newSort[0];

            if (count($newSort) > 1):
                $newSort = $newSort[1] == 1 || $newSort[1] == 2 ? $newSort[1] : $newSort[1] - 1;
            else:
                $newSort = $newSort[0] == 1 || $newSort[0] == 2 ? $newSort[0] : $newSort[0] - 1;
            endif;

            $page = str_split($event_image['to']);
            $pageNum = count($page) == 2 ? $page[0] : $page[1];

//            return $event_image['current_page'] .'    '. $pageNum;
            if ($event_image['current_page'] == $pageNum && $event_image['last_page'] > $event_image['current_page']):
                if ($output->ad_image_sort >= $event_image['from'] && $event_image['to'] >= $output->ad_image_sort):
                    array_splice($event_image['data'], $newSort, 0, $data); // splice in at position 3
                endif;

            endif;

//            $output->media = $event_image;
//            return $output;
//            $items = array_slice($event_image, request('pre_page',0), 10 );

            return $event_image;
        }
        return $event_image;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Model\event $event
     * @return \Illuminate\Http\Response
     */
    public function edit(event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Model\event $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = Request()->all();
        $rules = [
            'title' => 'required|String',
            'description' => 'required|String',
            'special_image' => 'mimes:jpeg,jpg,png,gif|max:10000',
            'video' => 'mimes:mp4,mov,ogg,qt | max:20000',
            'main_category_id' => 'required|Integer',
            // 'address' => 'required',
//             'phone' => 'required',
            'user_id' => 'required|In teger',
            'invitation_start_time' => 'date_format:Y-m-d H:i:s'
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 401);
        }
        //insert normal image
        if (isset($input['special_image'])) {
            $image = $input['special_image'];
            $image_name = 'media-' . rand(10, 100) . date('mdYhis') . '.' . pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);

            $image_path = 'image/event/';

            $photo = Image::make($image)
                ->fit(1600, 1400, function ($constraint) {
//            $constraint->aspectRatio();
                })->encode('jpg', 50);
            Storage::disk('public')->put($image_path . $image_name, $photo);

//            Storage::disk('public')->putFileAs($image_path, $image, $image_name);

            $input['special_image'] = '/storage/image/event/' . $image_name;

        }
        $event = event::where('id', $id)->first();
        Storage::delete($event['special_image']);
        $event->update($input);
        //insert media
        if (isset($input['media'])) {
            media::where('event_id', $id)->delete();
            for ($i = 0; $i < count($input['media']); $i++) {
                $media = $input['media'][$i];
                $media_name = 'media-' . ($i + 30 * 35) . '-' . rand(100, 1000) . '-' . ($i * 30 + 95) . '.' . $media->getClientOriginalName();
                $thump_name = 'thump-' . ($i + 30 * 35) . '-' . rand(100, 1000) . '-' . ($i * 30 + 95) . '.' . $media->getClientOriginalName();
                $thump_path = 'thump/';
                $media_path = 'image/event/';

                $height = Image::make($media)->height();
                $newWidth = ($height * 8) / 5;
                $thump = Image::make($media)
                    ->resize(600, 300, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode('jpg', 50);
                $photo = Image::make($media)
                    ->resize(600, 300, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode('jpg', 50);
                Storage::disk('public')->put($media_path . $media_name, $photo);
                $thump->save($thump_path . $thump_name);
//                Storage::disk('public')->putFileAs($media_path, $media, $media_name);

                $input['image'] = '/storage/image/event/' . $media_name;
                $input['thump'] = '/thump/' . $thump_name;
                $media = new media();
                $media->create(['event_id' => $event->id, 'image' => $input['image'], 'thump' => $input['thump']]);
            }

        }
        return ['state' => 202];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Model\event $event
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        event::where('id', $id)->where('user_id', Auth::id())->delete();
        return ['state' => 202];
    }


}
