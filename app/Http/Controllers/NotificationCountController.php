<?php

namespace App\Http\Controllers;

use App\Model\adsNotifications;
use App\Model\User;

class NotificationCountController extends Controller
{
    //
    public function NotificationCount()
    {
        $user = new User();
        if (isset(auth()->user()->id)) {
            $userData = $user->where('id', auth()->user()->id)->first();
            $last_seen_notification = $userData->notification != '' ? $userData->notification : '';
            $count_notification = adsNotifications::where('country_id', request('country_id'));
            if (request()->has('region_id')) {
                $count_notification = $count_notification->where('region_id', request('region_id'));
            }
            if ($last_seen_notification != '') :
                $count_notification = $count_notification->where('created_at', '>', $last_seen_notification);
            endif;
            $count_notification = $count_notification->count();
            $update_last_seen = $user->where('id', auth()->user()->id)->update([
                'notification' => date("Y-m-d H:i:s"),
            ]);
            return ['state' => 'success', 'count' => $count_notification];
        } else {
            return ['state' => 'success', 'count' => 0];
        }
    }
}
