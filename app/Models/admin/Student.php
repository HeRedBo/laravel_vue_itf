<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Support\Facades\DB;

class Student extends Model implements Transformable
{
    use TransformableTrait;
    protected $fillable = [];
    protected $appends = ['age'];
    
    protected  $tb_student_contacts = 'student_contacts';
    protected  $tb_student_card = 'student_card';
    protected  $tb_student_class = 'student_class';
    
    /**
     * 属于该学生的联系人
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author Red-Bo
     */
    public  function contacts()
    {
        return $this->hasMany(StudentContacts::class,'student_id');
    }
    
    public  function cards()
    {
        return $this->hasMany(StudentCard::class,'student_id');
    }
    
    public  function classes()
    {
        return $this->belongsToMany(Classes::class,'student_class','student_id','class_id');
    }

    public function operator()
    {
        return $this->hasOne(Admin::class, 'id', 'operator_id');
    }

    public  function  venues() {
        return $this->hasOne(Venue::class, 'id', 'venue_id');
    }
    
    /**
     * 获取学生年龄字段
     * @param $value
     * @return false|int
     * @author Red-Bo
     */
    public function getAgeAttribute($value)
    {
        $age = strtotime($this->birthday);
        list($y1,$m1,$d1) = explode("-",date("Y-m-d",$age));
        list($y2,$m2,$d2) = explode("-",date("Y-m-d"));
        $age = $y2 - $y1;
        if((int)($m2.$d2) < (int)($m1.$d1))
            $age -= 1;
        return $age;
    }

    /**
     * 学生联系人添加
     * @param array $contacts
     * @param  int $student_id
     * @return  bool
     * @author Red-Bo
     */
    public  function  giveContactsTo(array $contacts)
    {
        // 删旧添新
        $this->contacts()->delete();
        $student_contacts = [];
        foreach($contacts as $v)
        {
            $student_contacts[] = [
                'student_id'    => $this->id,
                'relation_id'   => $v['relation_id'],
                'contact_name'   => $v['contact_name'],
                'contact_phone'  => $v['contact_phone'],
                'contact_email' => isset($v['contact_email']) ? $v['contact_email'] : '',
                'created_at' => date("Y-m-d H:i:s")
            ];
        }
        DB::table($this->tb_student_contacts)->insert($student_contacts);
        return true;
    }
    
  
    /**
     * 学生卡券添加 卡券不能做新删旧添加 无 添加有不需要添加  生成卡券记录同时生成卡券ID
     * @param array $cards
     * @param  int $sign_up_time
     * @param int $number_card_id 会员开ID
     * @return bool
     * @author Red-Bo
     */
    public  function giveCardTo(array $cards, $sign_up_time, $number_card_id)
    {
        $student_card = [];
        $card_id_arr = array_column($cards,'card_id');
        $card_data = Card::whereIn('id', $card_id_arr)->get()->toArray();
        $card_data = array_column($card_data,NUll,'id');
        foreach ($cards as $card)
        {
            $card_info = isset($card_data[$card['card_id']]) ?$card_data[$card['card_id']] : [];
            if($card_info)
            {
                $card_type = $card_info['type'];
                $unit      = $card_info['unit'];
                $number    = $card_info['number'];
                $card_number = 0;
                $end_time  = '';
                $now       =  date("Y-m-d H:i:s");
                if($card['status'] == 1)
                {
                    if($card_type == 1)
                    {
                        $end_time  =  strtotime("$number $unit", strtotime($sign_up_time));
                        $end_time  =  date("Y-m-d H:i:s", $end_time);
                    }
                }
                if($card_type ==2)
                {
                    $card_number = $card_info['number'];
                }

                $card_tmp = [
                    'student_id'        => $this->id,
                    'number_card_id'    => $number_card_id,
                    'card_id'           => $card['card_id'],
                    'number'            => $card['number'],
                    'card_price'        => $card_info['card_price'],
                    'total_class_number' => $card_number,
                    'start_time'         => $sign_up_time,
                    'end_time'           => $end_time,
                    'status'             => $card['status'],
                    'operator_id'        =>  auth('admin')->user()->id,
                    'operator_name'      =>  auth('admin')->user()->name,
                    'updated_at'         => $now,
                ];
                if($card['id'])
                {
                    $id = $card['id'];
                    DB::table($this->tb_student_card)->where('id','=', $id)->update($card_tmp);
                }
                else
                {
                    $card_tmp['created_at'] = $now;
                    $student_card[] = $card_tmp;
                }
            }
        }
        if($student_card)
        {
            foreach ($student_card as $key => &$student) 
            {
                $id = DB::table($this->tb_student_card)->insertGetId($student);
                $student['id'] = $id;
            }
            return $student_card;   
        }
        return true;
    }
    
    /**
     * 学生班级增加与修改
     * @param array $classId
     * @return bool
     * @author Red-Bo
     */
    public function  giveClassTo(array $classId)
    {
        $classes = Classes::whereIn('id', $classId)->get();
        $student_class = [];
        foreach($classes as $v)
        {
            $student_class[] = [
                'student_id' => $this->id,
                'class_id'  => $v->id
            ];
        }
        $this->classes()->detach();
        DB::table($this->tb_student_class)->insert($student_class);
        return true;
    }
    
    /**
     * 图片链接地址重组
     * @param $pic
     * @return mixed
     * @author Red-Bo
     */
    public function getPictureAttribute($pic)
    {
        $manager = app('uploader');
        if(\Request::method() == 'PUT' || \Request::method() == "DELETE")
        {
            return $pic;
        }
        if ($pic)
        {
            return $manager->fileWebPath($pic);
        }
        else
        {
            return $manager->fileWebPath('files/avatar/default.png');
        }
    }
}
