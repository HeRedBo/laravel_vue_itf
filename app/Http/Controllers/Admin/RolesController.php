<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\RoleCreateRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Repositories\RoleRepository;



class RolesController extends ApiController
{

    /**
     * @var RoleRepository
     */
    protected $repository;


    public function __construct(RoleRepository $repository)
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
        try
        {
            //$this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
            $roles = $this->repository->paginate(20)->toArray();
            return $this->response->withData($roles);
        }
        catch (\Exception $e)
        {
            return $this->response->withInternalServer($e->getMessage());
        }
        //return view('roles.index', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  RoleCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(RoleCreateRequest $request)
    {
        $role = $this->repository->create($request->all());
        return $this->response->withCreated('角色创建成功');
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
        $role = $this->repository->find($id);
        if (request()->wantsJson()) {
            return response()->json([
                'data' => $role,
            ]);
        }
        return view('roles.show', compact('role'));
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
        $id   = $request->get('id');
        $role = $this->repository->find($id)->toArray();
        return $this->response->withData($role);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  RoleUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(RoleUpdateRequest $request, $id)
    {
        $res = $this->repository->updateRoleData($request->all(), $id);
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
        $deleted = $this->repository->delete($id);
        return $this->response->withSuccess('数据删除成功');
        if (request()->wantsJson()) {

            return response()->json([
                'message' => 'Role deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Role deleted.');
    }


    public function getAcl(Request $request)
    {
        $id = $request->get('id');
        $data = $this->repository->getAcl($id);
        if($data['status'] ==1 )
        {
            return $this->response->withData($data['data']);
        } 
        else 
        {
            return $this->response->withError($data['msg']);
        }
    }


    public function setAcl(Request $request)
    {
        
        $result = $this->repository->setAcl($request);
        if($result['status'] ==1)
        {
            return $this->response->withSuccess($result['msg']);
        }
        else 
        {
            return $this->response->withError($result['msg']);
        }
        
    }


    public function  checkRoleName(Request $request)
    {
        $name  = $request->get('name');
        $id    = $request->get('id');
        $status = 0;
        $check = $this->repository->checkRoleName($name,$id);
        if($check)
            $status = 1;
        
        $result['status'] = $status;
        return $this->response->withData($result);
    }

    
}
