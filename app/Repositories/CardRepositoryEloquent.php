<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\CardRepository;
use App\Models\Admin\Card;
use App\Validators\CardValidator;

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
     * @return void
     */
    public function createCard(array $data)
    {
        $card = $this->model;
        // 设置字段默认值
        foreach(array_keys($this->fields) as $field)
        {
            $card->$field = empty($data[$field]) ? $this->fields[$field] : $data[$field];
        }
        $res = $card->save();
        if($res)
            return success('卡券创建成功');
        else
            return error('卡券创建失败');
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
                return success('卡券修改成功');
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
}
