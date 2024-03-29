<?php

namespace App\Repositories;

use App\Services\Common\Dictionary;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\AdminCommonRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\CardRepository;
use App\Models\Admin\Card;
use App\Validators\CardValidator;
use App\Services\Logs\CardOperationLogServices;
use Illuminate\Support\Facades\Event;
use App\Events\AdminLogger;

/**
 * Class CardRepositoryEloquent
 * @package namespace App\Repositories;
 */
class CardRepositoryEloquent extends AdminCommonRepository implements CardRepository
{
    protected $fields = [
        'venue_id'      => 0,
        'type'          => 1,
        'name'          => '',
        'number'        => 0,
        'unit'          => '',
        'card_price'    => 0,
        'explain'       => '',
        'status'        => 0,
        'start_time'        => '',
        'end_time'        => '',
        'operator_id'   => 0,
    ];
    

    
    protected $attributeValue = [
        'type' => '卡券类型',
        'name' => '卡券名称',
        'number'  => '计算数据',
        'unit' => '卡券计算单位',
        'card_price' => '卡券价格',
        'status' => '卡券启用状态',
        'start_time' => '卡券有效期开始时间',
        'end_time' => '卡券有效期结束时间',
        'explain' => '卡券说明',
    ];
    
    protected $cardStatusMap = [
        '0' => '未启用',
        '1' => '启用',
    ];
    
    protected $fieldSearchable = [
        'name'=>'like',
        'type',
        'venue_id',
    ];

    const LOGGER_TYPE = 'card';
    const DEFAULT_PAGE_SIZE = 15;
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Card::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }


    /**
     * 添加卡券
     *
     * @param array  $data
     * @return  mixed
     */
    public function create(array $data)
    {
        $card = $this->model;
        // 设置字段默认值
        foreach(array_keys($this->fields) as $field)
        {
            $card->$field = empty($data[$field]) ? $this->fields[$field] : $data[$field];
        }
        try
        {
            DB::beginTransaction();
            $res = $card->save();
            if($res)
            {
                // 保存卡券操作日志：
                $card_log_services =  new CardOperationLogServices();
                $log_data = [
                    'card_id' => $card->id,
                    'operation' => '新增卡券',
                    'newValue' => "名称：{$data['name']},ID：{$card->id}"
                ];
                $res = $card_log_services->addCardLog($log_data);
                if($res['status'] == 1)
                {
                    DB::commit();
                    Event::fire(new AdminLogger($data['venue_id'],'create',"添加卡券【{$data['name']}】"));
                    return success('卡券创建成功');
                }
                else
                {
                    DB::rollBack();
                    return error($res['msg']);
                }
            }
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            logResult('【管理员数据创建失败】'. $e->__toString(),'error');
            return error($e->getMessage());
        }   
    }
    
    /**
     * 编辑卡券
     * @param array $data
     * @param       $id
     * @author Red-Bo
     */
    public function  update(array $data, $id)
    {
        $card = $this->find($id);
        if($card)
        {
            $old_card_data = $card->toArray();
            $uid =  auth('admin')->user()->id;
            // 卡券启用后不可编辑 超级管理员可以修改 
            // if($card->status == 1 && $uid != 1)
            //     return error('卡券一启用不能编辑');

            // 设置字段默认值
            foreach(array_keys($this->fields) as $field)
            {
                $card->$field = empty($data[$field]) ? $this->fields[$field] : $data[$field];
            }
            $res = $card->save();
            if($res)
            {
                $log_data = $this->buildCardLogData($old_card_data, $data);
                if($log_data)
                {
                    $card_log_services =  new CardOperationLogServices();
                    $card_log_services->addCardLog($log_data);
                }
                Event::fire(new AdminLogger($data['venue_id'],'create',"添加卡券【{$data['name']}】"));
                return success('卡券修改成功');
                
            }
            else
                return error('卡券创建失败');
            
        }
        else
        {
            return error('');
        }
        
       
    }

    public  function  checkCardName(array $params)
    {
        $name = $params['name'] ? $params['name'] : '';
        $id   = $params['id'] ? $params['id'] : 0;
        $venue_id = $params['venue_id'] ? $params['venue_id']: 0;
         $where = [
            'name'=> $name,
        ];
        if($id > 0) {
            $where[] = ['id','!=', $id];
        }
        if($venue_id)
            $where[] = ['venue_id','=', $venue_id];
        return  $this->findWhere($where)->toArray();
    }

    public  function  updateStatus($id, $status)
    {
        $card = $this->find($id);
        $old_card_data = $card->toArray();
        if($card)
        {
            if($card->status != $status)
            {
                $card->status = $status;
                $res = $card->save();
                $card = $card->toArray();
                
                if($res == false)
                {
                    return error('卡卷修改失败');
                }
            }
            $log_data = $this->buildCardLogData($old_card_data, $card,'修改卡券状态');
            if($log_data)
            {
                $card_log_services =  new CardOperationLogServices();
                $card_log_services->addCardLog($log_data);
            }
            Event::fire(new AdminLogger($card['venue_id'],'update',"修改卡券【{$card['name']}】状态为".$this->cardStatusMap[$status]));
            return success('卡卷修改成功');
        }
        return error('数据不存在');
    }


    public function  getCardLogger($request)
    {
        $type_id = $request->get('card_id');
        $where = [];
        if($type_id) {
            $where[] = ['type_id','=', $type_id];
        }
        $orderBy = $request->get('orderBy')?:'id';
        $sortBy  = $request->get('sortedBy')?:'desc';
        $pageSize = $request->get('pageSize') ?: self::DEFAULT_PAGE_SIZE;
        $operator_name  = $request->get('operator_name');
        $search_time   = $request->get('search_time');

        if($operator_name)
        {
            $where[] = ['operator_name','like',"%{$operator_name}%"];
        }
        if($search_time && is_array($search_time))
        {
            $where[] = ['created_at','>=', $search_time[0]];
            $where[] = ['created_at','<=', $search_time[1]];
        }

        $order_by = [
            [$orderBy,$sortBy]
        ];
        $cardLogServices = new CardOperationLogServices();
        return $cardLogServices->searchLog(self::LOGGER_TYPE, $where,$order_by,$pageSize);
    }
    protected function buildCardLogData(array $oldData, array $newData, $operation = '')
    {
        $operation = $operation ? $operation : '编辑卡券';
        $field = $newValues = $oldValues =[];
        foreach ($oldData as $k => $v)
        {
            if($newData[$k] != $v)
            {
                if($k == 'status')
                {
                    $oldData['status'] = $this->cardStatusMap[$v];
                    $newData['status'] = $this->cardStatusMap[$newData[$k]];
                }
                
                if(isset($this->attributeValue[$k]))
                {
                    $field[] = $this->attributeValue[$k];
                    $oldValues[] = $oldData[$k];
                    $newValues[] = $newData[$k];
                }
            }
        }
        $params = [
            'card_id'   => $oldData['id'],
            'operation' => $operation,
            'field'     => $field,
            'oldValue'  => $oldValues,
            'newValue'  => $newValues,
        ];
        return !empty($params['field']) ? $params : [];
    }
}
