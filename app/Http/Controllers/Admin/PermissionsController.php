<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use League\OAuth2\Server\RequestEvent;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\PermissionCreateRequest;
use App\Http\Requests\PermissionUpdateRequest;
use App\Repositories\PermissionRepository;


class PermissionsController extends ApiController
{
    /**
     * @var PermissionRepository
     */
    protected $repository;
    
    public function __construct(PermissionRepository $repository)
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
        
        $data['tree'] =  $this->repository->getTreeData();
        $data['select'] = $this->repository->getSelectList();
        return $this->response->withData($data);
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $permissions = $this->repository->all();

        if (request()->wantsJson()) 
        {

            return response()->json([
                'data' => $permissions,
            ]);
        }

        return view('permissions.index', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PermissionCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(PermissionCreateRequest $request)
    {
        $permission = $this->repository->create($request->all());
        return $this->response->withCreated('权限创建成功');
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
        $permission = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $permission,
            ]);
        }

        return view('permissions.show', compact('permission'));
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
        $permission = $this->repository->find($id)->toArray();
        return $this->response->withData($permission);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  PermissionUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(PermissionUpdateRequest $request, $id)
    {
        $permission = $this->repository->update($request->all(), $id);
        return $this->response->withSuccess('权限修改成功');
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
                'message' => 'Permission deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Permission deleted.');
    }
}
