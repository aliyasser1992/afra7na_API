<?php

namespace App\Http\Controllers;

use App\Model\adsNotifications;
use App\Model\notification;
use Illuminate\Http\Request;
use App\Model\User;
use Carbon\Carbon;
use DB;
class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {
//        if (auth()->check()) {
//                $output = adsNotifications::where('country_id', auth()->user()->country_id)->Orwhere('country_id' , 0);
//                $output = $output->where('region_id', auth()->user()->region_id)->Orwhere('region_id' , 0);
//                $user = User::find(auth()->user()->id);
//                $user->notification = Carbon::now()->format('Y-m-d H:i:s');
//                $user->save();
//        }
//       elseif (request('region_id') && request('country_id')){
//            $output = adsNotifications::where('region_id', request('region_id'))->ORwhere('region_id' , 0);
//            $output = $output->where('country_id', request('country_id'))->Orwhere('country_id' , 0);
//        }
//        $output =
               return  adsNotifications::orderBy('id', 'desc')
            ->offset(0)
            ->limit(20)
            ->get();
//        return $output;
//            ->paginate(10);
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
     * @param \App\Model\notification $notification
     * @return \Illuminate\Http\Response
     */
    public function show(notification $notification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Model\notification $notification
     * @return \Illuminate\Http\Response
     */
    public function edit(notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Model\notification $notification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, notification $notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Model\notification $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(notification $notification)
    {
        //
    }
}
