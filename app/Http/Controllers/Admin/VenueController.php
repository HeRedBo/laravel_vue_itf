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
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $venues = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $venues,
            ]);
        }

        return view('venues.index', compact('venues'));
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
        ]);
        $res = $this->repository->updateVenueData($data, $id);
        
        if($res['status'] == 1)
            return $this->response->withSuccess('数据更新成功');
        else
            return $this->response->withError($res['msg']);
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
        $deleted = $this->repository->delete($id);
        return $this->response->withSuccess('数据删除成功');
    }
}
