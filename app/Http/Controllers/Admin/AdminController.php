<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\AdminCreateRequest;
use App\Http\Requests\AdminUpdateRequest;
use App\Repositories\AdminRepository;
use App\Repositories\RoleRepository;
use App\Repositories\VenueRepository;
use App\Models\Admin\Admin;


class AdminController extends ApiController
{
    protected  $fields = [
        'username' => '',
        'name'     => '',
        'phone'    => '',
        'email'    => '',
        'picture'  => '',
        'roles'    => [],
    ];
    /**
     * @var AdminRepository
     */
    protected $repository;
    
    protected  $role;

    protected $venue; 

    /**
     * @var AdminValidator
     */
    protected $validator;

    public function __construct(
        AdminRepository $repository, 
        RoleRepository $role,
        VenueRepository $venue
    )
    {
        parent::__construct();
        $this->repository = $repository;
        $this->role = $role;
        $this->venue = $venue;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $admins = $this->repository->all();
        if (request()->wantsJson()) {

            return response()->json([
                'data' => $admins,
            ]);
        }

        return view('admins.index', compact('admins'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  AdminCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AdminCreateRequest $request)
    {
         $res = $this->repository->createAdminData($request->all());
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
        $admin = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $admin,
            ]);
        }

        return view('admins.show', compact('admin'));
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
        $id = $request->get('id');
        $result = $this->repository->getAdminInfo($id);
        if($result['status'] == 1)
        {
            return $this->response
                        ->setResponseData($result['data'])
                        ->withSuccess($result['msg']);
        } else
        {
            return $this->response->withError($res['msg']);
        }
    }
    
    /**
     * 获取所有的角色数据
     * @return \Illuminate\Http\JsonResponse
     * @author Red-Bo
     */
    public function role()
    {
        $roles = $this->role->all()->toArray();
        return $this->response->withData($roles);
    }
    
    /**
     * 获取道馆下拉列表
     * @return \Illuminate\Http\JsonResponse
     * @author Red-Bo
     */
    public  function  venues()
    {
        $columns  = ['id','name','logo'];
        $venues = $this->venue->all($columns)->toArray();
        return $this->response->withData($venues);
    }
        
    /**
     * Update the specified resource in storage.
     *
     * @param  AdminUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(AdminUpdateRequest $request, $id)
    {
        
        
        
        try
        {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $admin = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'Admin updated.',
                'data'    => $admin->toArray(),
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
                'message' => 'Admin deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Admin deleted.');
    }
}
