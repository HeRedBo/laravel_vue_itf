<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\CardCreateRequest;
use App\Http\Requests\CardUpdateRequest;
use App\Repositories\CardRepository;
use App\Validators\CardValidator;
use App\Services\Common\Dictionary;
use App\Services\Admin\StudentCard;


class CardsController extends ApiController
{

    /**
     * @var CardRepository
     */
    protected $repository;

    /**
     * @var CardValidator
     */
    protected $validator;

    protected $student_card_service;

    public function __construct(CardRepository $repository,StudentCard $student_card)
    {
        parent::__construct();
        $this->repository = $repository;
        $this->student_card_service = $student_card;
        
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       // $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        try
        {
            $cards = $this->repository
                ->with(['venues','operator'])
                ->paginate(20)
                ->toArray();
            if($cards['data'])
            {
                $data = $cards['data'];
                $unit_options = Dictionary::UnitOptions();
                foreach($data as $k => &$val) {
                    
                    $val['unit_str'] = isset($unit_options[$val['unit']]) ? $unit_options[$val['unit']] : '次';
                    $val['type_str'] = Dictionary::CardTyeMap($val['type']);
                }
                $cards['data'] = $data;
            }
            
            
            return $this->response->withData($cards);
        }
        catch (\Exception $e)
        {
            return $this->response->withInternalServer($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CardCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CardCreateRequest $request)
    {
        try 
        {
            $data = array_merge($request->all(), [
                'operator_id'      => auth('admin')->user()->id,
            ]);
            $res = $this->repository->create($data);
            if($res['status'] == 1)
                return $this->response->withCreated('数据创建成功');
            else
                return $this->response->withInternalServer($res['msg']);
            
        } catch (\Exception $e)
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
        $card = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $card,
            ]);
        }

        return view('cards.show', compact('card'));
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

        $card = $this->repository->find($id);

        return view('cards.edit', compact('card'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  CardUpdateRequest $request
     * @param  string            $id
     *
     * @return  \Illuminate\Http\JsonResponse
     */
    public function update(CardUpdateRequest $request, $id)
    {
        try 
        {
            $data = array_merge($request->all(), [
                'operator_id'      => auth('admin')->user()->id,
            ]);
            $res = $this->repository->update($data,$id);
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
                'message' => 'Card deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Card deleted.');
    }



    public function  checkCardName(Request $request)
    {
        $params = $request->all();
        $status = 0;
        $check = $this->repository->checkCardName($params);
        if($check)
            $status = 1;
        $result['status'] = $status;
        return $this->response->withData($result);
    }

    public function  changeStatus(Request $request)
    {
        $id = $request->get('id');
        $status = $request->get('status');
        $res = $this->repository->updateStatus($id, $status);
        if($res['status'] == 1)
            return $this->response->withSuccess($res['msg']);
        else
            return $this->response->withInternalServer($res['msg']);
    }

    public function getCardOptions(Request $request)
    {
        $venue_id = $request->get('venue_id');
        $fields = ['id','name','card_price','unit','status','number'];
        $where = [
            ['status' ,'=',1],
            ['venue_id' ,'=', $venue_id],
        ];
        $data = $this->repository->findWhere($where,$fields)->toArray();
        $data = array_column($data,NULL,'id');
        return $this->response->withData($data);
    }

    /**
     * 通过学生ID获取学生道馆卡券下拉框
     * @param Request $request
     * @return array
     */
    public  function  studentCardOptions(Request $request)
    {
        $params = $request->all();
        $res  = $this->student_card_service->getStudentCardOptions($params);
        if($res['status'] == 1)
        {
            $data  = $res['data'];
            $data = array_column($data,NULL,'id');
            return $this
                    ->response->setResponseData($data)
                    ->withSuccess($res['msg']);
        }
        else
            return $this->response->withInternalServer($res['msg']);


    }


    public  function  cardLogger(Request $request)
    {
        $data = $this->repository->getCardLogger($request);
        return $this->response->withData($data);
    }

     public function cardTypeOptions()
    {
        $cardTypeMap = Dictionary::CardTyeMap();
        return $this->response->withData($cardTypeMap);
    }
}
