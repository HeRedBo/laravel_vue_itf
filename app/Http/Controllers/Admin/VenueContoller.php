<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\VenueRepository;

class VenueContoller extends Controller
{
    
    protected $venue;

    protected $fields = [
        'name' => '',
        'federation_id' => '0',
        'logo' => '',
        'logo_thumb' => '',
        'parent_id' => '0',
        'province_code' => '0',
        'city_code' => '0',
        'district_code' => '0',
        'address' => '',
        'remark' => '',
        'operator_id' => 0,
    ]; 

    public function __construct(VenueRepository $venue)
    {
        $this->venue =  $venue;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(Requests\VenueRequest $request)
    {
        $data = array_merge($request->all(), [
            'user_id'      => \Auth::id(),
            'last_user_id' => \Auth::id()
        ]);
        
        foreach (array_keys($this->fields) as $key => $field) 
        {
            $articles->$field = $request->get($field, $this->fields[$field]);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
