<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\StudentRepository;
use App\Models\Admin\Student;
use App\Models\Admin\RelationName;

/**
 * Class StudentRepositoryEloquent
 * @package namespace App\Repositories;
 */
class StudentRepositoryEloquent extends BaseRepository implements StudentRepository
{
    protected $fields = [
        'name'          => '',
        'native_place'  => '',
        'sex'           => 1,
        'picture'       => '',
        'birthday'      => '',
        'id_card'       => '',
        'school'        => '',
        'province_code' => '',
        'province'      => '',
        'city_code'     => '',
        'city'          => '',
        'area_code'     => '',
        'area'          => '',
        'address'       => '',
        'sign_up_at'    => '',
        'venue_id'      => '',
        'status'        => 1,
    ];
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Student::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
    /**
     * create a new student
     * @param array $data
     * @author Red-Bo
     */
    public  function  createStudent(array  $data)
    {
        $student = $this->model;
        // 设置字段默认值
        foreach(array_keys($this->fields) as $field)
        {
            $student->$field = empty($data[$field]) ? $this->fields[$field] : $data[$field];
        }
        
        dd($student->toArray());
        //unset($student->use_contacts);
        //unset($student->user_cards);
        //保存用户信息
        $student->save();
        $studentContacts = $data['use_contacts'];
        $studentCards    = $data['user_cards'];
        $classId         = $data['class_id'];
        $sign_up_at    = $data['sign_up_at'];
        
        // 保存用户课程信息
        $student->giveClass($classId);
        // 保存用户联系人
        $student->giveContactsTo($studentContacts);
        // 保存用户购买的卡券想你想
        $student->giveCardTo($studentCards,$sign_up_at);
        return success('学生信息创建成功');
    }

    public  function  getRelationOptions()
    {
        return  RelationName::all(['id','name'])->toArray();
    }
}
