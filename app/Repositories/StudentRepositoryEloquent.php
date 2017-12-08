<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\StudentRepository;
use App\Models\Admin\Student;
use App\Models\Admin\Card;
use App\Models\Admin\RelationName;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


/**
 * Class StudentRepositoryEloquent
 * @package namespace App\Repositories;
 */
class StudentRepositoryEloquent extends BaseRepository implements StudentRepository
{
    protected $fields = [
        'name'          => '',
        'native_place'  => '',
        'sex'           => '',
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

    protected $fieldSearchable = [
        'name'=>'like',
        'sex',
        'venue_id',
    ];

    protected $pageSize = 15;


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


    public  function  studentList(Request $request)
    {
        $pageSize = $request->get('pageSize') ?: $this->pageSize;

        $data =   $this->with(['operator','venues','classes' => function($query) {
            //$query->where('name','like','%2017 秋季高级版%');
        }])
            ->paginate($pageSize)
            ->toArray();

        return $data;
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
                $student->$field = is_null($data[$field]) ? $this->fields[$field] : $data[$field];
            }
            //保存用户信息
            $student->save();
            $studentContacts = $data['user_contacts'];
            $studentCards    = $data['user_cards'];
            $classId         = $data['class_id'];
            $sign_up_at      = $data['sign_up_at'];
            $student_id      =   $student->id;
            
            // 处理用户会员卡问题
            
            //$studentContacts = json_decode($studentContacts,true);
            //$studentCards    = json_decode($studentCards,true);
            // 保存用户课程信息
            $student->giveClassTo($classId);
            // 保存用户联系人
            $student->giveContactsTo($studentContacts);
            //// 保存用户购买的卡券
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
            $old_picture = $student->picture;
            foreach(array_keys($this->fields) as $field)
            {
                if($field == 'picture')
                {
                    if(strrpos($data[$field],'http:') !== false) {
                        continue;
                    }
                }
                $student->$field = is_null($data[$field]) ? $this->fields[$field] : $data[$field];
            }

            if($old_picture != $data['picture'])
            {
                // 删除旧图
                $manager = app('uploader');
                $manager->deleteFile($old_picture);
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

//            $student['birthday'] = DateTimeToGmt($student['birthday']);
//            $student['sign_up_at'] = DateTimeToGmt($student['sign_up_at']);
            // 获取数据归属的班级
            $class = $student->classes;
            $classIds = array_column($class->toArray(),'id');
            $student['class_id'] = $classIds;
            // 获取用户卡券
            $student_cards = $student->cards->toArray();
            $card_ids = array_column($student_cards,'card_id');
            $cards_info = Card::whereIn('id',$card_ids)->get()->toArray();
            $cards_info = array_column($cards_info,NULL, 'id');

            foreach ($student_cards as &$student_card) {
                $card_info = $cards_info[$student_card['card_id']];
                $student_card['name'] = $card_info['name'];
                $student_card['buy_number'] = $student_card['number'];
                $student_card['card_price'] = $card_info['card_price'];
                $student_card['total_price'] = $student_card['number'] * $card_info['card_price'];
            }

            $contacts =  $student->contacts->toArray();
            $relations_ids = array_column($contacts,'relation_id');
            $relation_names = RelationName::whereIn('id', $relations_ids)->get()->toArray();
            $relation_names = array_column($relation_names,NULL,'id');

            foreach ($contacts as &$contact) {
                $contact['relation_name'] = $relation_names[$contact['relation_id']]['name'];
            }
            $student['user_cards'] = $student_cards;
            $student['user_contacts'] = $contacts;
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
