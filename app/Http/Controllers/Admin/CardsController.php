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

    public function __construct(CardRepository $repository)
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
        try
        {
            $cards = $this->repository
                ->with(['venues','operator'])
                ->paginate(20)
                ->toArray();
            
            
            if($cards['data'])
            {
                $data = $cards['data'];
                foreach($data as $k => &$val) {
                    
                    $val['unit_str'] = Dictionary::UnitOptions($val['unit']);
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
            $res = $this->repository->createCard($data);
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
     * @return Response
     */
    public function update(CardUpdateRequest $request, $id)
    {
        try 
        {
            $data = array_merge($request->all(), [
                'operator_id'      => auth('admin')->user()->id,
            ]);
            $res = $this->repository->updateCard($data,$id);

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
        $name  = $request->get('name');
        $id    = $request->get('id');
        $status = 0;
        $check = $this->repository->checkCardName($name,$id);
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

    public function getCardOptions()
    {
        $fields = ['id','name','card_price','unit','number'];
        $data = $this->repository->all($fields)->toArray();
        $data = array_column($data,NULL,'id');
        return $this->response->withData($data);
    }
}
