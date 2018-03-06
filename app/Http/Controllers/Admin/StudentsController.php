<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\StudentCreateRequest;
use App\Http\Requests\StudentUpdateRequest;
use App\Http\Requests\StudentSignRequest;
use App\Http\Requests\StudentSignCalendar;
use App\Http\Requests\StudentCardCreateRequest;
use App\Repositories\StudentRepository;
use App\Services\Common\Dictionary;
use App\Services\Admin\StudentCard;
use App\Services\Admin\VenueBillService;



class StudentsController extends ApiController
{

    /**
     * @var StudentRepository
     */
    protected $repository;
    protected $student_card_service;
    protected $billService ;


    public function __construct(StudentRepository $repository,StudentCard $student_card,VenueBillService $billService)
    {
        parent::__construct();
        $this->repository  = $repository;
        $this->student_card_service = $student_card;
        $this->billService = $billService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        try
        {
            $student = $this->repository->studentList($request);
            return $this->response->withData($student);
        }
        catch (\Exception $e)
        {
            logResult('【学生信息查询错误】'.$e->__toString(),'error');
            return $this->response->withInternalServer($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StudentCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StudentCreateRequest $request)
    {
        try
        {
            $data = array_merge($request->all(),
                ['operator_id' => auth('admin')->user()->id],
                ['operator_name' => auth('admin')->user()->name]
            );
            $res = $this->repository->createStudent($data ,$this->student_card_service, $this->billService);
            if($res['status'] == 1)
                return $this->response->withCreated($res['msg']);
            else
                return $this->response->withInternalServer($res['msg']);
        }
        catch (\Exception $e)
        {
            return $this->response->withInternalServer($e->getMessage());
        }
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
        $student = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $student,
            ]);
        }

        return view('students.show', compact('student'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result = $this->repository->getStudentInfo($id, $this->student_card_service);
        if($result['status'] == 1)
        {
            return $this
                    ->response->setResponseData($result['data'])
                    ->withSuccess($result['msg']);
        }
        else
        {
            return $this->response->withInternalServer($result['msg']);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  StudentUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(StudentUpdateRequest $request, $id)
    {
        try
        {
            $data = array_merge($request->all(),[
                'operator_id'      => auth('admin')->user()->id,
                'operator_name'    => auth('admin')->user()->name
            ]);
            $res = $this->repository->updateStudent($data, $id,$this->student_card_service);
            if($res['status'] == 1)
                return $this->response->withSuccess($res['msg']);
            else
                return $this->response->withInternalServer($res['msg']);
        }
        catch (\Exception $e)
        {
            return $this->response->withInternalServer($e->getMessage());
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
                'message' => 'Student deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Student deleted.');
    }

    public  function  relationOptions()
    {
        try{
            $data = $this->repository->getRelationOptions();
        }catch (\Exception $e)
        {
            dd($e);
        }

        $data = array_column($data, NULL ,'id');
        return $this->response->withData($data);
    }

    public function sexOptions()
    {
        $sexMap = Dictionary::SexOptions();
        return $this->response->withData($sexMap);
    }

    /**
     * 获取学生基本信息
     */
    public  function getStudentInfo(Request $request)
    {
        $student_id = $request->get('student_id');
        if(empty($student_id))
            return $this->response->withBadRequest("student_id 参数不能为空");
        $res = $this->repository->getStudentBaseInfo($student_id,$this->student_card_service);
        if($res['status'] == 1)
            return $this->response->withData($res['data']);
        else
            return $this->response->withInternalServer($res['msg']);

    }
    /**
     *  学生卡券信息列表
     */
    public  function  studentCardList(Request $request)
    {
        try
        {
            $list = $this->student_card_service->getStudentCardList($request);
            return $this->response->withData($list);
        }
        catch (\Exception $e)
        {
            logResult('【获取学生卡券信息错误】'.$e->__toString(),'error');
            return $this->response->withInternalServer($e->getMessage());
        }
    }

    /**
     * 创建学生卡券
     * @param StudentCardCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public  function  saveStudentCard(StudentCardCreateRequest $request)
    {
        $res = $this->student_card_service->saveStudentCard($request,$this->billService);
        if($res['status'] == 1)
            return $this->response->withCreated($res['msg']);
        else
            return $this->response->withInternalServer($res['msg']);
    }

    public  function  sign(StudentSignRequest $request)
    {
        $params = $request->all();
        $res = $this->repository->sign($params);
        if($res['status'] == 1)
            return $this->response->withSuccess($res['msg']);
        else
            return $this->response->withInternalServer($res['msg']);
    }


    public  function  getSignCalendar(StudentSignCalendar $request)
    {
        $res = $this->repository->getSignCalendar($request);
        if($res['status'] == 1)
            return $this->response->withData($res['data']);
        else
            return $this->response->withInternalServer($res['msg']);
    }

    public function signClassOptions(Request $request)
    {
        $this->repository->signClassOptions($request);
    }


}
