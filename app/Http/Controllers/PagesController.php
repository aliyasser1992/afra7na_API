<?php

namespace App\Http\Controllers;

use App\Model\pages;
use Illuminate\Http\Request;
class PagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return pages::all();
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\pages  $pages
     * @return \Illuminate\Http\Response
     */
    public function show(pages $pages)
    {
        return pages::where('id',$id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\pages  $pages
     * @return \Illuminate\Http\Response
     */
    public function edit(pages $pages)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\pages  $pages
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, pages $pages)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\pages  $pages
     * @return \Illuminate\Http\Response
     */
    public function destroy(pages $pages)
    {
        //
    }
}
