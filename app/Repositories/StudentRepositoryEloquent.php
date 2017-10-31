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
    
        //unset($student->use_contacts);
        //unset($student->user_cards);
        //保存用户信息
        $student->save();
        $studentContacts = $data['use_contacts'];
        $studentCards    = $data['user_cards'];
        
        //
        
    
    
    }

    public  function  getRelationOptions()
    {
        return  RelationName::all(['id','name'])->toArray();
    }
}
