<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PermissionRepository;
use App\Models\Admin\Permission;
use App\Validators\PermissionValidator;
use Cache;

/**
 * Class PermissionRepositoryEloquent
 * @package namespace App\Repositories;
 */
class PermissionRepositoryEloquent extends BaseRepository implements PermissionRepository
{

    protected $fields = [
        'name' => '',
        'display_name' => '',
        'parent_id' => 0,
        'icon' => '',
        'is_show' => 0,
        'order_num' => 0,
     ];


    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Permission::class;
    }
    
    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
    /**
     * 获取jstree josn 数据
     * @return arrary
     */
    public function getTreeData()
    {
        $data = [];
        $list = $this->orderBy('order_num','ASC')
                ->all()
                ->toArray();
        foreach ($list as $k => $v)
        {
            if($v['parent_id'] == 0) {
                $data[$k]['id'] = $v['id'];
                $data[$k]['parent'] = '#';
                $data[$k]['text'] = $v['display_name'];
                $data[$k]['state'] = ['opened' => true];
            } else {
                $data[$k]['id'] = $v['id'];
                $data[$k]['parent'] = $v['parent_id'];
                $data[$k]['text'] = $v['display_name'];
            }
        }
        return $data;
    }
    
    
    public function getSelectList()
    {
        $list = $this->orderBy('order_num', 'ASC')->all()
                     ->toArray();
        return $this->_reSort($list, 0, 0);
    }
    
    private function _reSort($data, $parent_id, $level = 0)
    {
        static $arr = [['label' => '根', 'value' => 0]];
        foreach ($data as $k => $v) {
            if ($v['parent_id'] == $parent_id) {
                $v['level'] = $level;
                $display_name = str_repeat('--', $level) . $v['display_name'];
                $arr[] = ['label' => $display_name, 'value' => $v['id']];
                unset($data[$k]);
                $this->_reSort($data, $v['id'], $level + 1);
            }
        }
        return $arr;
    }


    public function createPermissionData(array $data)
    {
        $permisson = $this->model;
        // 设置字段默认值
        foreach(array_keys($this->fields) as $field)
        {
            $permisson->$field = empty($data[$field]) ? $this->fields[$field] : $data[$field];
        }
        $permisson->save();
        Cache::forget('menus');
        return success('数据创建成功');
    }

    public function updatePermissionData(array $data, $id)
    {
        $permisson = $this->find($id);
        if($permisson)
        {
            // 设置字段默认值
            foreach(array_keys($this->fields) as $field)
            {
                $permisson->$field = empty($data[$field]) ? $this->fields[$field] : $data[$field];
                if($field == 'is_show') {
                    $permisson->$field = (bool) $permisson->$field;
                } 
                
            }
            $permisson->save();
            Cache::forget('menus');            
            return success('数据更新成功');
        }
        return error('记录不存在，请检查');
    }
}
