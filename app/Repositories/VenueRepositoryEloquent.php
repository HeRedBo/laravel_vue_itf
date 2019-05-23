<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\AdminCommonRepository;
use App\Repositories\VenueRepository;
use App\Models\Admin\Venue;
use  App\Criteria\VenueCriteria;

/**
 * Class VenueRepositoryEloquent
 * @package namespace App\Repositories;
 */
class VenueRepositoryEloquent extends AdminCommonRepository implements VenueRepository
{
    protected $fields = [
        'name' => '',
        'federation_id' => '0',
        'logo' => '',
        'logo_thumb' => '',
        'parent_id' => '0',
        'card_prefix' => '',
        'province_code' => '0',
        'province' => '',
        'city' => '',
        'city_code' => '0',
        'area_code' => '0',
        'area' => '0',
        'address' => '',
        'remark' => '',
        'operator_id' => 0,
        'operator_name' => '',
    ];

    protected $fieldSearchable = [
        'name'=>'like',
        'province',
    ];

    protected $pageSize = 15;
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
            $venue->$field = (!isset($data[$field]) || empty($data[$field])) ? $this->fields[$field] : $data[$field];
        }

        try 
        {
            $venue->save();
        }
        catch (\Exception $e) 
        {
            logResult('【道馆新增失败】'. $e->__toString(),'error');
            return error($e->getMessage());
        }
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
            $old_logo = $venue->logo;
            $old_logo_thumb = $venue->logo_thumb;
            // 设置字段默认值
            foreach(array_keys($this->fields) as $field)
            {
                if( in_array($field, ['logo','logo_thumb']))
                {
                    if((strrpos($data[$field],'https:') !== false) || strrpos($data[$field],'http:') !== false) {
                        continue;
                    }
                }
                $venue->$field = empty($data[$field]) ? $this->fields[$field] : $data[$field];
            }
            $logo = $data['logo'];
            $logo_thumb = $data['logo_thumb'];
            $manager = app('uploader');
            if(!(strrpos($data['logo'],'http:') !== false || strrpos($data['logo'],'https:') !== false) &&  $old_logo != $logo)
            {
                // 删除旧图
                $manager->deleteFile($old_logo);
            }

            // 删除旧的缩略图
            if(!(strrpos($data['logo_thumb'],'http:') !== false || strrpos($data['logo_thumb'],'https:') !== false) && $old_logo_thumb != $logo_thumb)
            {
                $manager->deleteFile($old_logo_thumb);
            }
            
            $venue->save();
            return success('数据更新成功');
        }
        return error('记录不存在，请检查');
    }
    

    public function VenueList($request)
    {
        $pageSize = $request->get('pageSize') ?: $this->pageSize;
        return  $this->with('operator')
                    ->paginate($pageSize)
                    ->toArray(); 
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


    /**
     * 获取管理员管理的的道馆数
     *
     * @param int $is_self
     * @param array $where
     * @return int
     */
    public function getNumber($is_self = 1, $where = [])
    {
        $whereIn = [];
        if($is_self)
        {
            $venue_ids = $this->getUserVenueIds();
            if($venue_ids)
            {
                $whereIn[] = ['id', $venue_ids];
            }
        }
        $query = $this->model->query();
        if($whereIn)
        {
            foreach ($whereIn as $in)
            {
                $query->whereIn($in[0], $in[1]);
            }
        }
        if($where)
        {
            foreach ($where as $v)
            {
                $query->where($v[0], $v[1], $v[2]);
            }
        }
        return $query->count();
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
