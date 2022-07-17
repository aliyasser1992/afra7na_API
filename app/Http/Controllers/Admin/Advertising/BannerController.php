<?php

namespace App\Http\Controllers\Admin\Advertising;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Advertising\Banner\BannerStoreRequest;
use App\Http\Requests\Admin\Advertising\Banner\BannerUpdateRequest;
use App\Model\Banner;
use Exception;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /* $banners = Banner::with([
            'advertiser' => function ($query) {
                return $query->select('id', 'name','category_id');
            },
            'event' => function($query) {
                return $query->select('id', 'title', 'region_id');
            },
            'event.region' => function($query) {
                return $query->select('id', 'title_ar');
            },
            'advertiser.category' => function($query) {
                return $query->select('id', 'title_ar');
            }
        ])
        ->paginate(10); */
        $search = $request->search;
        $limit = $request->limit ?? 10;
        $sort = $request->sort ?? 'created_at';
        $order = $request->order ?? 'asc';

        $banners = DB::table('banners')
        ->select([
            'banners.*',
            'advertiser.name as advertiser_name',
            'regions.title_ar as region',
            'category.title_ar as advertiser_category',
            'event.title as event_title',
            'event.id as event_id',
        ])
        ->join('advertisers as advertiser', 'advertiser.id', '=', 'banners.advertiser_id')
        ->join('events as event', 'event.id', '=', 'banners.event_id')
        ->leftJoin('ads_category as category', 'category.id', '=', 'advertiser.category_id')
        ->leftJoin('regions', 'regions.id', '=', 'event.region_id')
        ->when($search, function ($query) use($search) {
            $query->where('advertiser.name', 'LIKE', "%$search%")
            ->orWhere('category.title_ar', 'LIKE', "%$search%")
            ->orWhere('event.title', "LIKE", "%$search%");
        })
        ->where('banners.deleted_at', null)
        ->orderBy($sort, $order)
        ->paginate($limit);

        return response()->json($banners);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BannerStoreRequest $request)
    {
        $validated = $request->validated();
        try {
            DB::beginTransaction();
            $banner = Banner::create($validated);

            if ($request->has('photo')) {


                $path = $request->file('photo')->store('image/banners');
                //$path = Storage::putFile('image', $request->file('photo'));
                $banner->link = 'storage/'.$path;
                $banner->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'تم حفظ الاعلان',
            ]);
        } catch (Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json([
                'message' => $e,
            ],500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $banner = Banner::with(['advertiser', 'event'])->findOrFail($id);
        return $banner;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BannerUpdateRequest $request, $id)
    {
        $validated = $request->validated();
        try {
            DB::beginTransaction();
            $banner = Banner::findOrFail($id);
            $banner->update($validated);

            if ($request->has('photo')) {

                if ($banner->link && file_exists(public_path($banner->link))) {
                    unlink(public_path($banner->link));
                }

                $path = $request->file('photo')->store('image/banners');
                //$path = Storage::putFile('image', $request->file('photo'));
                $banner->link = 'storage/'.$path;
                $banner->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'تم حفظ الاعلان',
            ]);
        } catch (Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json([
                'message' => $e,
            ],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        if ($banner->link && file_exists(public_path($banner->link))) {
            unlink(public_path($banner->link));
        }

        $banner->delete();

        return response()->json([
            'message' => 'تم الحذف',
        ]);
    }
}
