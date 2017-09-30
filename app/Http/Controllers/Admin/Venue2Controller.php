<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\VenueRepository;

class VenueController extends ApiController
{
    
    protected $venue;



    public function __construct(VenueRepository $venue)
    {
        parent::__construct();
        $this->venue =  $venue;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->response->withData($this->venue->page());
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
            'operator_id'      => auth('admin')->user()->id,
        ]);
        $this->venue->store($data);
        return $this->response->withCreated('数据创建成功');
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
    public function edit(Request $request)
    {
        $id      = $request->get('id');
        $item    = $this->venue->getRowByPK($id)->toArray();
        return $this->response->withData($item);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\VenueRequest $request, $id)
    {
        $data = array_merge($request->all(), [
            'operator_id'      => auth('admin')->user()->id,
        ]);
        $this->venue->update($id, $data);
        return $this->response->withSuccess('数据修改成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->venue->destroy($id);
        return $this->response->withSuccess('数据删除成功');
    }
}
