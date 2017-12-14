<?php

namespace App\Repositories;

use Mockery\CountValidator\Exception;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ClassesRepository;
use App\Models\Admin\Classes;
use App\Validators\ClassesValidator;
use Illuminate\Support\Facades\DB;

/**
 * Class ClassesRepositoryEloquent
 * @package namespace App\Repositories;
 */
class ClassesRepositoryEloquent extends BaseRepository implements ClassesRepository
{
    protected  $fields = [
        'name' => '',
        'venue_id' => 0,
        'remark' => '',
        'status' => 1,
        'operator_id' => 0,
    ];
    
    protected $fieldSearchable = [
        'status',
        'name'=>'like'
    ];
    
    protected  $tb_admin = 'admin';
    protected  $tb_admin_venue = 'admin_venue';
    protected  $tb_class = 'classes';
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Classes::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }


    /**
     * 创建课程数据
     * @param array $data
     * @return array
     */
    public  function  createClass(array $data)
    {
        $class = $this->model;
        // 设置字段默认值
        foreach(array_keys($this->fields) as $field)
        {
            $class->$field = empty($data[$field]) ? $this->fields[$field] : $data[$field];
        }
        $res = $class->save();
        if($res)
        {
            return success('班级创建成功');
        }
        else{
            return error('班级数据创建失败');
        }
    }

    public  function  updateClass(array $data, $id)
    {
        $class = $this->find($id);
        if($class)
        {
            try
            {
                foreach(array_keys($this->fields) as $field)
                {
                    $class->$field = (!isset($data[$field])) ? $this->fields[$field] : $data[$field];
                }
                // 保存用户信息
                $res = $class->save();
                if($res)
                {
                    return success('班级创建成功');
                }
                else
                {
                    return error('班级数据创建失败');
                }
            }
            catch (\Exception $e)
            {
                logResult('【数据编辑错误】'. $e->__toString());
                return error($e->getMessage());
            }
        }
        else
        {
            return error('数据不存在');
        }
    }
    
    /**
     * 删除课程数据 课程被启用 不能被删除
     * @param $id
     * @author Red-Bo
     */
    public  function  deleteClasses($id)
    {
        $classes = $this->find($id);
        try
        {
            if($classes)
            {
                // 这里以后需要添加 课程是否被引用的判断
                $res = $this->delete($id);
                if($res !== false)
                    return success('数据删除成功');
            }
        }catch (\Exception $e)
        {
            logResult('【课程删除服务器错误】'.$e->__toString());
            return error($e->getMessage());
        }
        return error('数据不存在 无法删除');
    }
    
    public  function  checkClassName($name,$id)
    {
        $where = [
            'name'=> $name,
        ];
        if($id > 0) {
            $where[] = ['id','!=', $id];
        }
        return  $this->findWhere($where)->toArray();
    }
    
    /**
     * 获取用户归属道馆的下拉框
     * @param $venue_id
     * @author Red-Bo
     */
    public function  getVenueClassOptions($venue_id)
    {
        // 获取用户
     
    }
}
