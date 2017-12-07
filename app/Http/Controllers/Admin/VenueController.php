<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\VenueCreateRequest;
use App\Http\Requests\VenueUpdateRequest;
use App\Repositories\VenueRepository;
use App\Validators\VenueValidator;

class VenueController extends ApiController
{

    /**
     * @var VenueRepository
     */
    protected $repository;

    /**
     * @var VenueValidator
     */
    protected $validator;

    public function __construct(VenueRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try
        {
            $list = $this->repository->VenueList($request);
            return $this->response->withData($list);
        }catch (\Exception $e)
        {
            return $this->response->withInternalServer($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  VenueCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(VenueCreateRequest $request)
    {
        $data = array_merge($request->all(), [
            'operator_id'      => auth('admin')->user()->id,
            'operator_name'      => auth('admin')->user()->name
        ]);
        $res = $this->repository->createVenueData($data);
        if($res['status'] == 1)
            return $this->response->withCreated('数据创建成功');
        else
            return $this->response->withError($res['msg']);
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $venue = $this->repository->find($id);
        if (request()->wantsJson()) {

            return response()->json([
                'data' => $venue,
            ]);
        }

        return view('venues.show', compact('venue'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $id      = $request->get('id');
        $venue   = $this->repository->find($id)->toArray();
        return $this->response->withData($venue);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  VenueUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(VenueUpdateRequest $request, $id)
    {
        $data = array_merge($request->all(), [
            'operator_id'      => auth('admin')->user()->id,
            'operator_name'      => auth('admin')->user()->name
        ]);
        $res = $this->repository->updateVenueData($data, $id);
        
        if($res['status'] == 1)
            return $this->response->withSuccess('数据更新成功');
        else
            return $this->response->withInternalServer($res['msg']);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->repository->delete($id);
        return $this->response->withSuccess('数据删除成功');
    }

    public function getVenueOptions()
    {
        $data = $this->repository->getTreeData();
        return $this->response->withData($data);
    }
    
    public function  checkVenueName(Request $request)
    {
        $name  = $request->get('name');
        $id    = $request->get('id');
       
        $status = 0;
        $check = $this->repository->checkVenueName($name,$id);
        if($check)
            $status = 1;
        
        $result['status'] = $status;
        return $this->response->withData($result);
    }
}
