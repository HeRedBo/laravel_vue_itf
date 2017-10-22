<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ClassesCreateRequest;
use App\Http\Requests\ClassesUpdateRequest;
use App\Repositories\ClassesRepository;


class ClassesController extends ApiController
{

    /**
     * @var ClassesRepository
     */
    protected $repository;



    public function __construct(ClassesRepository $repository)
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
        $classes = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $classes,
            ]);
        }

        return view('classes.index', compact('classes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ClassesCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ClassesCreateRequest $request)
    {
        $data = array_merge($request->all(), [
            'operator_id'      => auth('admin')->user()->id,
        ]);
        $res = $this->repository->createClass($data);
        if($res['status'] == 1)
            return $this->response->withCreated('数据创建成功');
        else
            return $this->response->withInternalServer($res['msg']);

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
        $class = $this->repository->find($id);
        if (request()->wantsJson()) {

            return response()->json([
                'data' => $class,
            ]);
        }
        return view('classes.show', compact('class'));
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

        $class = $this->repository->find($id);

        return view('classes.edit', compact('class'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  ClassesUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(ClassesUpdateRequest $request, $id)
    {

        $data = array_merge($request->all(), [
            'operator_id'      => auth('admin')->user()->id,
        ]);
        $res = $this->repository->updateClass($data,$id);
        if($res['status'] == 1)
            return $this->response->withSuccess('数据修改成功');
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
        $deleted = $this->repository->delete($id);
        if (request()->wantsJson()) {

            return response()->json([
                'message' => 'Classes deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Classes deleted.');
    }
}
