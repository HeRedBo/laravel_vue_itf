<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\StudentNumberCardCreateRequest;
use App\Http\Requests\StudentNumberCardUpdateRequest;
use App\Repositories\StudentNumberCardRepository;
use App\Validators\StudentNumberCardValidator;


class StudentNumberCardController extends Controller
{

    /**
     * @var StudentNumberCardRepository
     */
    protected $repository;

    /**
     * @var StudentNumberCardValidator
     */
    protected $validator;

    public function __construct(StudentNumberCardRepository $repository, StudentNumberCardValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $studentNumberCards = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $studentNumberCards,
            ]);
        }

        return view('studentNumberCards.index', compact('studentNumberCards'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StudentNumberCardCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StudentNumberCardCreateRequest $request)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $studentNumberCard = $this->repository->create($request->all());

            $response = [
                'message' => 'StudentNumberCard created.',
                'data'    => $studentNumberCard->toArray(),
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
        $studentNumberCard = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $studentNumberCard,
            ]);
        }

        return view('studentNumberCards.show', compact('studentNumberCard'));
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

        $studentNumberCard = $this->repository->find($id);

        return view('studentNumberCards.edit', compact('studentNumberCard'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  StudentNumberCardUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(StudentNumberCardUpdateRequest $request, $id)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $studentNumberCard = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'StudentNumberCard updated.',
                'data'    => $studentNumberCard->toArray(),
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
                'message' => 'StudentNumberCard deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'StudentNumberCard deleted.');
    }
}
