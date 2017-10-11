<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\VenueRepository;
use App\Models\Admin\Venue;

/**
 * Class VenueRepositoryEloquent
 * @package namespace App\Repositories;
 */
class VenueRepositoryEloquent extends BaseRepository implements VenueRepository
{
    protected $fields = [
        'name' => '',
        'federation_id' => '0',
        'logo' => '',
        'logo_thumb' => '',
        'parent_id' => '0',
        'province_code' => '0',
        'province' => '',
        'city' => '',
        'city_code' => '0',
        'area_code' => '0',
        'area' => '0',
        'address' => '',
        'remark' => '',
        'operator_id' => 0,
    ];
    
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Venue::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
    /**
     * 道馆新增
     * @param array $data
     * @return array
     * @author Red-Bo
     */
    public  function  createVenueData(array $data)
    {
        
        $venue = $this->model;
        // 设置字段默认值
        foreach(array_keys($this->fields) as $field)
        {
            $venue->$field = empty($data[$field]) ? $this->fields[$field] : $data[$field];
        }
        $venue->save();
        return success('数据创建成功');
    }
    
    /**
     * 道馆数据更新
     * @param array $data 需要更新的数据
     * @param int   $id
     * @author Red-Bo
     */
    public  function  updateVenueData(array $data, $id)
    {
        $venue = $this->find($id);
        if($venue)
        {
            // 设置字段默认值
            foreach(array_keys($this->fields) as $field)
            {
                $venue->$field = empty($data[$field]) ? $this->fields[$field] : $data[$field];
            }
            $venue->save();
            return success('数据创建成功');
        }
        return error('记录不存在，请检查');
    }
    
    public  function checkVenueName($name, $id)
    {
        $where = [
            'name'=>$name,
        ];
        if($id > 0) {
            $where[] = ['id','!=', $id];
        }
        return  $this->findWhere($where)->toArray();
    }
    public function getTreeData()
    {
        $fields = ['id','name','parent_id','created_at','updated_at'];
        $venues = $this->all($fields)->toArray();
        return  $this->_reSort($venues);
    }
    
    private function _reSort($data, $parent_id=0, $level=0, $isClear=TRUE)
	{
		static $ret = array();
		if($isClear)
			$ret = array();
		foreach ($data as $k => $v)
		{
			if($v['parent_id'] == $parent_id)
			{
				$v['level'] = $level;
				$ret[] = $v;
				$this->_reSort($data, $v['id'], $level+1, FALSE);
			}
		}
		return $ret;
	}
}
