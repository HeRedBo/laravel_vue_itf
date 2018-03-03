<?php

namespace App\Models\admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class VenueStudentSign extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'admin_venue_student_sign';

    /**
     * 该模型是否被自动维护时间戳
     * @var bool
     */
    public $timestamps = false;

    // 字段白名单
    protected $fillable = [
        'venue_id','student_id','class_id','sign_date','status',
        'remark','operator_id','created_at',
    ];


    public  function  BatchInsert(array $data)
    {
        $insert_data = [];
        foreach ($data as $k => $val)
        {
            $insert_data[] = array_only($val, $this->fillable);
        }
        $DB = DB::table($this->table);
        return $DB->insert($insert_data);
    }



}
