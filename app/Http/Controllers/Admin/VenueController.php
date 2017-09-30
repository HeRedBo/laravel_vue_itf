<?php

namespace App\Http\Controllers;

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
        
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $venue = $this->repository->create($request->all());

            $response = [
                'message' => 'Venue created.',
                'data'    => $venue->toArray(),
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
    public function edit($id)
    {

        $venue = $this->repository->find($id);

        return view('venues.edit', compact('venue'));
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

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $venue = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'Venue updated.',
                'data'    => $venue->toArray(),
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
                'message' => 'Venue deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Venue deleted.');
    }
}
