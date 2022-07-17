<?php

namespace App\Http\Controllers\Admin\Backup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function create()
    {
        Artisan::call('backup:run',[]);
        return [Artisan::output()];
    }

    public function files(Request $request)
    {
        $request->validate([
            'disk' => 'required|string'
        ]);

        $disks = array_keys(config('filesystems.disks'));
        if (!in_array($request->disk, $disks)) {
            return response()->json([
                'message' => "Disk {$request->disk} is not valid disk"
            ], 400);
        }

        /**
         * @var FileSystem
         */
        $storage = Storage::disk($request->disk);

        $filesPath = $storage->allFiles();
        $files = [];
        foreach($filesPath as $file) {
            $files[] = [
                'name' => $file,
                'url' => $this->generateUrl($request->disk,$storage->url($file)),
                'size' => $storage->size($file)/(1000*1000),
                'last_date_modified' => $this->formatLastModifiedData($storage->lastModified($file)),

            ];
        }
        $files = collect($files)->sortBy('last_date_modified', SORT_DESC,true)->toArray();

        return response()->json( array_values($files));
    }

    private function formatLastModifiedData($date)
    {
        $date = DateTime::createFromFormat("U",$date);
        $timezone = new DateTimeZone(config('app.timezone'));

        return $date->setTimezone($timezone)
        ->format('20y-m-d h:i:s a');

    }

    function generateUrl($disk, $url) {
        if ($disk === 'backup') {
            return url($url);
        }
        return $url;
    }
}
