<?php

namespace App\Repositories;

use Mockery\CountValidator\Exception;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ClassesRepository;
use App\Models\Admin\Classes;
use App\Validators\ClassesValidator;

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
        'operator_id' => 0,
    ];
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
                    $class->$field = empty($data[$field]) ? $this->fields[$field] : $data[$field];
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
}
