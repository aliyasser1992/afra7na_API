<?php

namespace App\Http\Controllers;

use App\Model\flash_ads;
use Illuminate\Http\Request;
use DB;

class FlashAdsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $checkTurn = flash_ads::where('flag', request('flag'))
            ->where('country_id', request('country_id'))
            ->where('turn', 1)
            ->get();
        $totalFlash = flash_ads::where('flag', request('flag'))->get();
        $flash_ads = flash_ads::select('id', 'image', 'link', DB::raw('view as views'));
        if (count($checkTurn) > 0):
//            return '$checkTurn';
            $flash_ads->where('id', '!=', $checkTurn['0']['id'])
                ->where('id', '>', $checkTurn['0']['id']);
        endif;
        $flash_ads = $flash_ads
            ->where('country_id', request('country_id'))
            ->where('flag', request('flag'));
        $flash_ads = $flash_ads->first();
        if (empty($flash_ads)):
//            return 'empty => $flash_ads';
            $flash_ads = flash_ads::select('id', 'image', 'link', DB::raw('view as views'));
            if (count($checkTurn) > 0):
//                return '$checkTurn > 0';
                $flash_ads->where('id', '!=', $checkTurn['0']['id'])
                    ->where('id', '<', $checkTurn['0']['id']);
            endif;
            $flash_ads = $flash_ads->where('flag', request('flag'))
                ->where('country_id', request('country_id'));
            $flash_ads = $flash_ads->first();
            if (!empty($flash_ads)):
//                return'!empty($flash_ads)';
                flash_ads::find($flash_ads->id)->update(['turn' => null, 'view' => flash_ads::find($flash_ads->id)->view + 1]);
            endif;
        else:
//            return 'empty($flash_ads)';
//           return $flash_ads->id;
            $check = flash_ads::where('flag', request('flag'))->get();
            if (count($check) == 1):
                flash_ads::where('id', $flash_ads->id)->update(['turn' =>null, 'view' => flash_ads::find($flash_ads->id)->view + 1]);
            else:
                flash_ads::where('id', $flash_ads->id)->update(['turn' => 1, 'view' => flash_ads::find($flash_ads->id)->view + 1]);
            endif;
        endif;

        if (count($checkTurn) > 0 && count($totalFlash) > 1):
//            return '$checkTurn';
            $update = flash_ads::find($flash_ads->id)->update(['turn' => 1]);
            $update2 = flash_ads::find($checkTurn['0']['id'])->update(['turn' => null]);
        endif;
//       broadcast(new FlashMessages($flash_ads));
        return empty($flash_ads) ? [] : $flash_ads;
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
     * @param \App\Model\flash_ads $flash_ads
     * @return \Illuminate\Http\Response
     */
    public function show(flash_ads $flash_ads)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Model\flash_ads $flash_ads
     * @return \Illuminate\Http\Response
     */
    public function edit(flash_ads $flash_ads)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Model\flash_ads $flash_ads
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, flash_ads $flash_ads)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Model\flash_ads $flash_ads
     * @return \Illuminate\Http\Response
     */
    public function destroy(flash_ads $flash_ads)
    {
        //
    }
}
