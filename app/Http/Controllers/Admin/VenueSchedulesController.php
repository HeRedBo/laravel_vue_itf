<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\VenueScheduleCreateRequest;
use App\Http\Requests\VenueScheduleUpdateRequest;
use App\Repositories\VenueScheduleRepository;


class VenueSchedulesController extends ApiController
{

    /**
     * @var VenueScheduleRepository
     */
    protected $repository;


    public function __construct(VenueScheduleRepository $repository)
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
        $venueSchedules = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $venueSchedules,
            ]);
        }

        return view('venueSchedules.index', compact('venueSchedules'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  VenueScheduleCreateRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(VenueScheduleCreateRequest $request)
    {
        //$data_json = '{"course_times":[null,["2018-02-07 01:06:51","2018-02-07 02:06:51"],["2018-02-07 02:07:57","2018-02-07 04:07:57"]],"venue_schedules":[null,[null,{"week":1,"section":1,"remark":null,"class_id":4}],[null,{"week":2,"section":1,"remark":null,"class_id":3}],null,null,null,[null,{"week":6,"section":1,"remark":null,"class_id":4},{"week":6,"section":2,"remark":null,"class_id":4}],[null,{"week":7,"section":1,"remark":null,"class_id":5},{"week":7,"section":2,"remark":null,"class_id":3}]],"venue_course_form":{"venue_id":1,"date_between":["2018-02-07 00:00:00","2018-03-31 00:00:00"],"course_count":"2","schedule_name":"2018\u5e74\u65b0\u6625\u5b63\u8bfe\u7a0b\u8868","status":1}}';
        //$request_data = json_decode($data_json,true);
        //$res = $this->repository->create($request_data);
        
        
        $res = $this->repository->create($request->all());
        if($res['status'] == 1)
            return $this->response->withCreated($res['msg']);
        else
            return $this->response->withError($res['msg']);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $venueSchedule = $this->repository->find($id);
        if (request()->wantsJson()) {
            return response()->json([
                'data' => $venueSchedule,
            ]);
        }

        return view('venueSchedules.show', compact('venueSchedule'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $result = $this->repository->show($id);
        if($result['status'])
        {
            return $this->response->setResponseMessage($result['msg'])->withData($result['data']);
        }
        else
        {
            return $this->response->withInternalServer($result['msg']);
        }
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  VenueScheduleUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(VenueScheduleUpdateRequest $request, $id)
    {
        try
        {
            $venueSchedule = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'VenueSchedule updated.',
                'data'    => $venueSchedule->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
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

        if (request()->wantsJson()) {

            return response()->json([
                'message' => 'VenueSchedule deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'VenueSchedule deleted.');
    }
}
