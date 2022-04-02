<?php

namespace App\Http\Controllers;

use App\Model\event;
use App\Model\region;
use App\Model\User;
use App\Model\UserEvents;
use DB;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private function GetLsatEventDatesCreated()
    {
        $event = new event();
        $getLastEvents = $event
            ->select('created_at')
            ->where('country_id', request('country_id'))
            ->where('created_at', '>=', date("Y-m-d"))
            ->orderBy('id', 'DESC')
            ->get();
//
        $DateArr = array();
        foreach ($getLastEvents as $key => $dates) {
            $DateArr[] = $dates->created_at->format('Y-m-d H:i:s');
        }

        return $DateArr;
    }

    private function GetLastUserLastSeen()
    {
        $user_events = new UserEvents();
//                    DB::enableQueryLog();
        $userData = $user_events
            ->select(DB::raw('id,user_id,country_id,region_id,max(created_at) as created_at '))
            ->where('user_id', auth()->user()->id)
            ->where('country_id', request('country_id'))
            ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . " -48 hours")))
            ->where('created_at', '<=', date('Y-m-d H:i:s'))
            ->groupBy('region_id')
            ->get();
//            dd(DB::getQuerylog());


        return $userData;
    }

    private function GetRegionsBasedOnCountry()
    {
        $region = new region();
        return $region->select('id')->where('country_id', request('country_id'))->get();
    }

    private function GetEventsCountIfLastSeenFound($lastSeen, $region_id)
    {
//        return $lastSeen .': ' .$region_id;
//        DB::enableQueryLog();

        $count_events = event::select(DB::raw('count(region_id) as event_count, region_id'))
            ->where('region_id', $region_id)
            ->where('created_at', '>=', $lastSeen)
            ->whereRaw(DB::raw('(deleted_at IS NOT NULL and deleted_at <> "")'))
            ->withTrashed()
            ->first();
//        dd(DB::getQuerylog());

        return $count_events;
    }

    private function GetEventsCountBetweenTodayAnd48HoursAgo($region_id)
    {
//        DB::enableQueryLog();

        $count_events = event::select(DB::raw('count(region_id) as event_count, region_id'))
            ->where('region_id', $region_id)
            ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . " -48 hours")))
            ->where('created_at', '<=', date('Y-m-d H:i:s'))
            ->whereRaw(DB::raw('(deleted_at IS NOT NULL and deleted_at <> "")'))
            ->withTrashed()
            ->first();
//        dd(DB::getQuerylog());

        return $count_events;
    }

    public function index(Request $request)
    {
        $input = Request()->all();
        $user = new User();
        $count_weeding = 0;
        //Get Count of Events Between User Last Seen & Events Created_At
        // if Send User in Headers
        $countObj = array();
        $countObj48 = array();

        // if (isset(auth()->user()->id)) {
        //     $userLastSeen = $this->GetLastUserLastSeen();
        //     $regions = $this->GetRegionsBasedOnCountry();

        //     foreach ($regions as $k => $region) {
        //         if (count($userLastSeen) > 0):
        //             foreach ($userLastSeen as $K2 => $lastSeen) {
        //                 if ($lastSeen->region_id == $region->id) :
        //                     $countData = $this->GetEventsCountIfLastSeenFound($lastSeen->created_at->format('Y-m-d H:i:s'), $lastSeen->region_id);
        //                     $countObj[] = array(
        //                         'region_id' => $lastSeen->region_id,
        //                         'event_count' => $countData->event_count
        //                     );
        //                 endif;
        //             }
        //         endif;
        //         $countData = $this->GetEventsCountBetweenTodayAnd48HoursAgo($region->id);
        //         $countObj48[] = array(
        //             'region_id' => $region->id,
        //             'event_count' => $countData->event_count
        //         );

        //     }
        // }

        $id_arr = array();
        $final_arr = array();

        $this->checkmemberidArray($countObj, $id_arr, $final_arr);
        $this->checkmemberidArray($countObj48, $id_arr, $final_arr);

//       return $final_arr;

        $output = region::where('country_id', $input['country_id'])->get();
        $final_array = array();
//        return $countObj;
        foreach ($output as $key => $data) {
            $final_array[] = array(
                'id' => $data->id,
                'title_ar' => $data->title_ar,
                'title_en' => $data->title_en,
                'country_id' => $data->country_id,
                'event_count' => 0
            );
            if (isset(auth()->user()->id)) {
                foreach ($final_arr as $key2 => $count) {
//                    return $count;
                    if ($data->id == $count['region_id']) {
                        $final_array[$key]['event_count'] = $count['event_count'];
                    }
                }
            }

        }
        return $final_array;
    }

    public function checkmemberidArray($arr, &$id_arr, &$final_arr)
    {
        foreach ($arr as $key => $value) {
            if (!in_array($value['region_id'], $id_arr)) {
                $id_arr[] = $value['region_id'];
                $final_arr[] = $value;
            }
        }
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Model\region $region
     * @return \Illuminate\Http\Response
     */
    public function show(region $region)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Model\region $region
     * @return \Illuminate\Http\Response
     */
    public function edit(region $region)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Model\region $region
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, region $region)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Model\region $region
     * @return \Illuminate\Http\Response
     */
    public function destroy(region $region)
    {
        //
    }
}
