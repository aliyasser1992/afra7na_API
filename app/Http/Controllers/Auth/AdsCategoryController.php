<?php

namespace App\Http\Controllers;

use App\Model\ads;
use App\Model\ads_category;
use App\Model\User;
use Illuminate\Http\Request;

class AdsCategoryController extends Controller
{
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
                    'ads' =>date("Y-m-d H:i:s")
                ]);
            }else{
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
        $output = ads_category::all();
        $output[] = $countObj;

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
     * @param \App\Model\ads_category $ads_category
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Model\ads_category $ads_category
     * @return \Illuminate\Http\Response
     */
    public function edit(ads_category $ads_category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Model\ads_category $ads_category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ads_category $ads_category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Model\ads_category $ads_category
     * @return \Illuminate\Http\Response
     */
    public function destroy(ads_category $ads_category)
    {
        //
    }
}
