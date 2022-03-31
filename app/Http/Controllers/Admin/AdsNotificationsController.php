<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\adsNotifications;
use App\Model\region;
use App\Model\User;
use DB;
use FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Message\Topics;

//use OneSignal;

class AdsNotificationsController extends Controller
{
    //
    public function __construct(adsNotifications $adsNotifications, region $region, User $User)
    {
        $this->ads_notifications = $adsNotifications;
        $this->region = $region;
        $this->user = $User;
    }

    public function RegionByCountryID($id)
    {
        $output = $this->region->where('country_id', $id)->get();
        return response([
            'code' => 202,
            'state' => 'success',
            'data' => $output
        ]);
    }

    public function Get()
    {
        $output = $this->ads_notifications
            ->select('ads_notifications.*', 'countries.title_ar', DB::raw('regions.title_ar as region_title'))
            ->leftjoin('countries', 'countries.id', '=', 'ads_notifications.country_id')
            ->leftjoin('regions', 'regions.id', '=', 'ads_notifications.region_id')
            ->get();
        return response([
            'code' => 202,
            'state' => 'success',
            'data' => $output
        ]);
    }

    public function Set()
    {
        $input = Request()->all();
        $output_insert = $this->ads_notifications->create($input);
        // call function to send Notifications;
        $output_data = $this->ads_notifications
            ->select('ads_notifications.*', 'countries.title_ar', DB::raw('regions.title_ar as region_title'))
            ->leftjoin('countries', 'countries.id', '=', 'ads_notifications.country_id')
            ->leftjoin('regions', 'regions.id', '=', 'ads_notifications.region_id')
            ->where('ads_notifications.id', $output_insert->id)
            ->first();
//        $this->SendNotification($input['text'], $input['country_id'], $input['region_id']);
        $country_id = $output_data->country_id;
        $region_id = $output_data->region_id;
        $text = $output_data->text;
//                DB::enableQuerylog();
        if ($country_id != 0 && $region_id == 0) {
            $notification_count = $this->CountNotificationByCountry($country_id);
            $this->TopicNotification((string)"country_id_" . $country_id, $text, (string)$notification_count);
        } else if ($country_id != 0 && $region_id != 0) {
            // function return all users in this regions with last seen of notification
//            $users = $this->GetUsersLastSeenByRegionId($region_id);
//            //for loop for each user to calculate count of notification for each user based on last seen
//            foreach ($users as $key => $lastSeen) {
//                $badge = $this->GetCountOfNotificationBasedOnLastSeenOfUser($lastSeen['notification'],$region_id);
////                echo $badge ."\r\n";
//                //then send push notification for this topic
//                // but in each time how many notification send to user ??!!!!
//            }
            $badge = $this->CountNotificationsByRegions($region_id);
            $this->TopicNotification((string)"region_id_" . $region_id, $text, (string)$badge);

        } else {
            $this->TopicNotification('afr7na', $text);
        }


        if ($output_data):
            return response([
                'code' => 202,
                'state' => 'success',
                'data' => $output_data
            ]);

        endif;

    }

    public function TopicNotification($topic_send, $text, $notification_count)
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);
        $notificationBuilder = new PayloadNotificationBuilder(trans('اشعار جديد'));
        $dataBuilder = new PayloadDataBuilder();
        $notificationBuilder
            ->setBody($text)
            ->setSound('default')
            ->setBadge($notification_count);
        $dataBuilder->addData(
            [
                'title' => trans('notifications.title'),
                'message' => $text,
                'type' => 'new notification',
            ]);
        $notification = $notificationBuilder->build();
//        return $notification;
        $option = $optionBuilder->build();
        $data = $dataBuilder->build();
        $topic = new Topics();
        $topic->topic($topic_send);
//return ['topic' => $topic];
        $topicResponse = FCM::sendToTopic($topic, null, $notification, null);
//        return ['data' => $topicResponse];
        echo $topicResponse->isSuccess();
        echo $topicResponse->shouldRetry();
        echo $topicResponse->error();
//        return ['state' => $topicResponse];

    }

    public function CountNotificationsByRegions($id)
    {
//        return date('Y-m-d');
//        DB::enableQueryLog();
        return $this->ads_notifications->where('region_id', $id)
            ->where('created_at', '>=', date('Y-m-d'))
            ->count();
//         dd(DB::getQuerylog());
    }

    public function CountNotificationByCountry($id)
    {
        return $this->ads_notifications->where('country_id', $id)
            ->where('created_at', '>=', date('Y-m-d'))
            ->count();
    }

    private function GetUsersLastSeenByRegionId($id)
    {
        return User::where('region_id', $id)->get();
    }

    private function GetCountOfNotificationBasedOnLastSeenOfUser($lastSeen, $region_id)
    {
        return adsNotifications::where('region_id', $region_id)->where('created_at', '>', $lastSeen)->count();
    }


    public function DeleteNotification($id)
    {
        $output = $this->ads_notifications->where('id', $id)->delete();
        if ($output):
            return response([
                'code' => 202,
                'state' => 'success',
                'data' => []
            ]);
        endif;

    }

    public function SendNotification($text, $country_id, $region_id)
    {
//        DB::enableQuerylog();

        $output = $this->user->where('country_id', $country_id);
        if ($region_id != 0) {
            $output = $output->where('region_id', $region_id);
        }
        $output = $output->get();

        foreach ($output as $key => $user) {
            echo $user->id;
            $output = OneSignal::sendNotificationUsingTags(
                $text,
                array(
                    ["field" => "tag", "key" => "userId", "relation" => "=", "value" => $user->id]
                ),
                $url = null,
                $data = null,
                $buttons = null
//                $schedule = $event['invitation_start_time']
            );

            echo $output;
        }

//        dd(DB::getQuerylog());
    }

    // function to send

    public function OldNotification()
    {
//        $output = $this->user->where('country_id', $country_id);
//        if ($region_id != 0) {
//            $output = $output->where('region_id', $region_id);
//        }
//        $output = $output->get();
//
//        foreach ($output as $key => $user) {
////            echo $user->id;
//            $output = OneSignal::sendNotificationUsingTags(
//                $text,
//                array(
//                    ["field" => "tag", "key" => "userId", "relation" => "=", "value" => $user->id]
//                ),
//                $url = null,
//                $data = null,
//                $buttons = null
////                $schedule = $event['invitation_start_time']
//            );
//
//            echo $output;
//        }
    }

}
