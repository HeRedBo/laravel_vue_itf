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
    
    public  function  cards()
    {
        return $this->hasMany(StudentContacts::class,'student_id');
    }
    
    public  function classes()
    {
        return $this->belongsToMany(Classes::class,'student_class','student_id','class_id');
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
        //$relation_ids = array_column($contacts,'relation_id');
        //$relations = RelationName::whereIn(['id',$relation_ids])->get();
        foreach($contacts as $v)
        {
            $student_contacts[] = [
                'student_id'    => $this->id,
                'relation_id'   => $v['relation_id'],
                'contact_name'   => $v['contact_name'],
                'contact_phone'  => $v['contact_phone'],
                'contact_email' => $v['contact_email'],
            ];
        }
        DB::table($this->tb_student_contacts)->insert($student_contacts);
        return true;
    }
    
  
    /**
     * 学生卡券添加 卡券不能做新删旧添加 无 添加 有 不需要添加
     * @param array $cards
     * @param  int $student_id
     * @author Red-Bo
     */
    public  function giveCardTo(array $cards, $sign_up_time)
    {
        $student_card = [];
        $card_id_arr = array_column($cards,'id');
        $cards = array_column($cards,NULL,'id');
        $card_data = Card::whereIn('id', $card_id_arr)->get();
        foreach ($cards as $card)
        {
            $user_card =$cards[$card->id];
            $unit   = $user_card['unit'];
            $number = $user_card['number'];
            $end_time = strtotime($sign_up_time)  + strtotime("$number $unit");
            $end_time = date("Y-m-d H:i:s", $end_time);
            $student_card[] = [
                'student_id' => $this->id,
                'card_id' => $card->id,
                'number' => $user_card['number'],
                'start_time' => $sign_up_time,
                'end_time' => $end_time,
                'status' => 1,
                'operator_id' =>  auth('admin')->user()->id,
            ];
        }
        DB::table($this->tb_student_card)->insert($student_card);
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
        
        $this->classes()->detach();
        $classes = Claess::whereIn('id', $classId)->get();
        $student_class = [];
        foreach($classes as $v)
        {
            $student_class[] = [
                'student_id' => $this->id,
                'class_id'  => $v->id
            ];
        }
        DB::table($this->tb_student_class)->insert($student_class);
        return true;
    }
    
    


}
