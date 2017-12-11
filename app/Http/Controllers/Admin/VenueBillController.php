<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\VenueBillCreateRequest;
use App\Http\Requests\VenueBillUpdateRequest;
use App\Repositories\VenueBillRepository;


class VenueBillController extends Controller
{

    /**
     * @var VenueBillRepository
     */
    protected $repository;


    public function __construct(VenueBillRepository $repository) {
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
        $venueBills = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $venueBills,
            ]);
        }

        return view('venueBills.index', compact('venueBills'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  VenueBillCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(VenueBillCreateRequest $request)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $venueBill = $this->repository->create($request->all());

            $response = [
                'message' => 'VenueBill created.',
                'data'    => $venueBill->toArray(),
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
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $venueBill = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $venueBill,
            ]);
        }

        return view('venueBills.show', compact('venueBill'));
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

        $venueBill = $this->repository->find($id);

        return view('venueBills.edit', compact('venueBill'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  VenueBillUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(VenueBillUpdateRequest $request, $id)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);
            $venueBill = $this->repository->update($request->all(), $id);
            $response = [
                'message' => 'VenueBill updated.',
                'data'    => $venueBill->toArray(),
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
                'message' => 'VenueBill deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'VenueBill deleted.');
    }

    // 道馆战队数据类型创建
    
    public function createVenueBillDataType()
    {
        
    }
}
