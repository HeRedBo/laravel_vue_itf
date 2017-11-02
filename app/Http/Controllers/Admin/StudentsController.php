<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\StudentCreateRequest;
use App\Http\Requests\StudentUpdateRequest;
use App\Repositories\StudentRepository;
use App\Validators\StudentValidator;


class StudentsController extends ApiController
{

    /**
     * @var StudentRepository
     */
    protected $repository;

    /**
     * @var StudentValidator
     */
    protected $validator;

    public function __construct(StudentRepository $repository)
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
        $students = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $students,
            ]);
        }

        return view('students.index', compact('students'));
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
                ['operator_id' => auth('admin')->user()->id]
            );
            $res = $this->repository->createStudent($data);
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
        $result = $this->repository->getStudentInfo($id);
        if($result['status'] == 1)
        {
            return $this->response->setResponseData($result['data'])
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
            ]);
            $res = $this->repository->updateStudent($data, $id);
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
        $data = $this->repository->getRelationOptions();
        $data = array_column($data, NULL ,'id');
        return $this->response->withData($data);
    }

}
