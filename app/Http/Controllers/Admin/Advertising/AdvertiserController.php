<?php

namespace App\Http\Controllers\Admin\Advertising;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Advertiser;
use App\Http\Requests\Admin\Advertising\Advertiser\AdvertiserStoreRequest;
use App\Http\Requests\Admin\Advertising\Advertiser\AdvertiserUpdateRequest;

class AdvertiserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $advertisers = Advertiser::with(['banners', 'category','region'])
        ->paginate(20);

        return $advertisers;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdvertiserStoreRequest $request)
    {
        $data = $request->validated();
        $data['all_categories'] = $request->all_categories === "true" ? true: false;
        $data['all_regions'] = $request->all_regions === "true" ? true: false;
        $advertiser = Advertiser::create($data);

        return response()->json([
            'message' => 'تم اضافة المعلن'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $advertiser = Advertiser::with(['category', 'region'])
        ->findOrFail($id);

        return $advertiser;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdvertiserUpdateRequest $request, $id)
    {
        $data = $request->validated();
        $data['all_categories'] = $request->all_categories;
        $data['all_regions'] = $request->all_regions;

        $advertiser = Advertiser::findOrFail($id);
        $advertiser->update($data);

        return response()->json([
            'message' => 'تم تعديل المعلن'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $advertiser = Advertiser::findOrFail($id);
        $advertiser->delete();
        return response()->json([
            'message' => 'Deleted'
        ]);
    }


    public function getList()
    {
        $advertisers = Advertiser::select('id', 'name', 'website')->get();

        return response($advertisers);
    }
}
