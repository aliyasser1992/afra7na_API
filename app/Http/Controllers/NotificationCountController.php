<?php

namespace App\Http\Controllers;

use App\Model\adsNotifications;
use App\Model\User;

class NotificationCountController extends Controller
{
    //
    public function NotificationCount()
    {
        $user = request()->user();

        if (!$user) {
            return response()->json([
                'state' => 'Success',
                'count' => 0,
                'message' => 'No Authenticated user'
            ]);
        }

        $counter = adsNotifications::where('country_id', $user->country_id)
        ->when($user->notification, function ($query) use($user) {
            $query->where('created_at', '>', $user->notification);
        })
        ->count("*");

        $user->update([
            'notification' => date('Y-m-d H:i:s')
        ]);

        return response()->json([
            'state' => 'Success',
            'count' => $counter,
            'user' => $user
        ]);


        // $user = new User();
        // if (isset(request()->user()->id)) {
        //     $userData = $user->where('id', request()->user()->id)->first();
        //     $last_seen_notification = $userData->notification != '' ? $userData->notification : '';
        //     $count_notification = adsNotifications::where('country_id', auth()->user()->country_id);
        //     if (auth()->user()->region_id) {
        //         $count_notification = $count_notification->where('region_id', auth()->user()->region_id);
        //     }
        //     if ($last_seen_notification != '') :
        //         $count_notification = $count_notification->where('created_at', '>', $last_seen_notification);
        //     endif;
        //     $count_notification = $count_notification->count();
        //     $update_last_seen = $user->where('id', request()->user()->id)->update([
        //         'notification' => date("Y-m-d H:i:s"),
        //     ]);
        //     return ['state' => 'success', 'count' => $count_notification];
        // } else {
        //     return ['state' => 'success', 'count' => 0];
        // }
    }
}
