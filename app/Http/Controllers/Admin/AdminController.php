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
        try
        {
            $users = $this->repository->paginate(20)->toArray();
            return $this->response->withData($users);
        }
        catch (\Exception $e)
        {
            return $this->response->withInternalServer($e->getMessage());
        }
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
        $res = $this->repository->getAdminInfo($id);
        if($res['status'] == 1)
            return $this->response->withData($res['data']);
        else
            return $this->response->withInternalServer($res['msg']);
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
            return $this->response->withInternalServer($res['msg']);
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
    
    
    public function getUserVenues()
    {
        $uid = auth('admin')->user()->id;
        $res = $this->repository->getUserVenues($uid);
        if($res['status'] == 1)
        {
            return $this->response->withData($res['data']);
        }
        return $this->response->withInternalServer($res['msg']);
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
            $res = $this->repository->updateAdminData($request->all(), $id);
            if($res['status'] == 1)
                return $this->response->withSuccess('数据更新成功');
            else
                return $this->response->withInternalServer($res['msg']);
           
        } catch (\Exception $e) {
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
        
        $res = $this->repository->deleteUser($id);
        if($res['status'] == 1)
            return $this->response->withSuccess($res['msg']);
        else
            return $this->response->withInternalServer($res['msg']); 
    }
    
    public function checkUserName(Request $request) 
    {
        $name  = $request->get('username');
        $id    = $request->get('id');
       
        $status = 0;
        $check = $this->repository->checkUserName($name,$id);
        if($check)
            $status = 1;
        
        $result['status'] = $status;
        return $this->response->withData($result);
    }

    public  function logger(Request $request)
    {
        $res = $this->repository->logger($request);
        if($res['status'] == 1)
            return $this->response->withData($res['data']);
        else
            return $this->response->withInternalServer($res['msg']);

    }
}
