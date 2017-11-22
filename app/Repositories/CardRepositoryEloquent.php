<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\CardRepository;
use App\Models\Admin\Card;
use App\Validators\CardValidator;
use App\Services\Logs\CardOperationLogServices;

/**
 * Class CardRepositoryEloquent
 * @package namespace App\Repositories;
 */
class CardRepositoryEloquent extends BaseRepository implements CardRepository
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
        'operator_id'   => 0,
    ];
    
    protected $attributeValue = [
        'type' => '卡券类型',
        'name' => '卡券名称',
        'number'  => '计算数据',
        'unit' => '卡券计算单位',
        'card_price' => '卡券价格',
        'explain' => '卡券说明',
        'status' => '卡券说明',
    ];
    protected $fieldSearchable = [
        'name'=>'like',
        'venue_id',
    ];
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
    public function createCard(array $data)
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
    public function  updateCard(array $data, $id)
    {
        $card = $this->find($id);
        if($card)
        {
            $old_card_data = $card->toArray();
            $uid =  auth('admin')->user()->id;
            // 卡券启用后不可编辑 超级管理员可以修改 
            if($card->status == 1 && $uid != 1)
                return error('卡券一启用不能编辑');
            
            // 设置字段默认值
            foreach(array_keys($this->fields) as $field)
            {
                $card->$field = empty($data[$field]) ? $this->fields[$field] : $data[$field];
            }
            $res = $card->save();
            if($res)
            {
                //$log_data = $this->buildCardLogData($old_card_data, $data);
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

    public  function  checkCardName($name,$id)
    {
        $where = [
            'name'=> $name,
        ];
        if($id > 0) {
            $where[] = ['id','!=', $id];
        }
        return  $this->findWhere($where)->toArray();
    }

    public  function  updateStatus($id, $status)
    {
        $card = $this->find($id);
        if($card)
        {
            if($card->status != $status)
            {
                $card->status = $status;
                $res = $card->save();
                if($res == false)
                {
                    return error('卡卷修改失败');
                }
            }
            return success('卡卷修改成功');
        }
        return error('数据不存在');
    }
    
    protected function buildCardLogData(array $oldData, array $newData)
    {
        foreach ($oldData as $k => $v)
        {
            if($newData[$k] != $v) {
                if(isset($this->attributeValue[$k]))
                {
                    
                }
            }
        }
    }
}
