<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\StudentRepository;
use App\Models\Admin\Student;
use App\Models\Admin\RelationName;
use Illuminate\Support\Facades\DB;

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
        'province_code' => 0,
        'province'      => '',
        'city_code'     => 0,
        'city'          => '',
        'area_code'     => 0,
        'area'          => '',
        'address'       => '',
        'sign_up_at'    => '',
        'venue_id'      => '',
        'status'        => 1,
        'operator_id'   => 0
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
     * create a new student record
     * @param array $data
     * @return array|void
     * @author Red-Bo
     */
    public  function  createStudent(array  $data)
    {
        try
        {
            //
            DB::beginTransaction();
            $student = $this->model;
            // 设置字段默认值
            foreach(array_keys($this->fields) as $field)
            {
                $student->$field = empty($data[$field]) ? $this->fields[$field] : $data[$field];
            }
            //保存用户信息
            $student->save();
            $studentContacts = $data['user_contacts'];
            $studentCards    = $data['user_cards'];
            $classId         = $data['class_id'];
            $sign_up_at      = $data['sign_up_at'];
            //
            //$studentContacts = json_decode($studentContacts,true);
            //$studentCards    = json_decode($studentCards,true);
            // 保存用户课程信息
            $student->giveClassTo($classId);
            // 保存用户联系人
            $student->giveContactsTo($studentContacts);
            //// 保存用户购买的卡券想你想
            $student->giveCardTo($studentCards,$sign_up_at);
            DB::commit();
            return success('学生信息创建成功');
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            logResult('【学生信息新增错误】'. $e->__toString(),'error');
            return error($e->getMessage());
        }
    }
    
    /**
     * update student info
     * @param array $data
     * @param  int     $id
     * @return array|void
     * @author Red-Bo
     */
    public function  updateStudent(array $data, $id)
    {
        $student = $this->find($id);
        if(!$student)
        {
            return error('未找到相关数据记录信息');
        }
        
        try
        {
            DB::beginTransaction();
            // 设置字段默认值
            foreach(array_keys($this->fields) as $field)
            {
                $student->$field = empty($data[$field]) ? $this->fields[$field] : $data[$field];
            }
            //修改用户信息
            $student->save();
            $studentContacts = $data['user_contacts'];
            $studentCards    = $data['user_cards'];
            $classId         = $data['class_id'];
            $sign_up_at      = $data['sign_up_at'];
            //
            //$studentContacts = json_decode($studentContacts,true);
            //$studentCards    = json_decode($studentCards,true);
            //
            // 保存用户课程信息
            $student->giveClassTo($classId);
            // 保存用户联系人
            $student->giveContactsTo($studentContacts);
             //保存用户购买的卡券想
            $student->giveCardTo($studentCards,$sign_up_at);
            DB::commit();
            return success('数据修改成功');
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            logResult('【学生信息更新错误】'. $e->__toString(),'error');
            return error($e->getMessage());
        }
    }
    
    public  function  getRelationOptions()
    {
        return  RelationName::all(['id','name'])->toArray();
    }
    
    /**
     * get student info
     * @param integer  $id student_id
     * @author Red-Bo
     */
    public  function  getStudentInfo($id)
    {
        $student = $this->with(['classes','cards','contacts'])->find($id);
        if($student)
        {
            // 获取数据归属的班级
            $class = $student->classes;
            $classIds = array_column($class->toArray(),'id');
            $student['class_id'] = $classIds;
            // 获取用户卡券
            $student['userCards'] = $student->cards->toArray();
            $student['user_contacts'] = $student->contacts->toArray();
            $student = $student->toArray();
            unset($student['cards']);
            unset($student['contacts']);
            unset($student['classes']);
            return success('数据获取成功', $student);
        }
        else
        {
            return error('学生信息不存在');
        }
    }
}
