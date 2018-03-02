<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class VenueSchedule extends Model implements Transformable
{
    use TransformableTrait;
    protected $fillable = ['venue_id','course_count','schedule_name','start_time','end_time','status','operator_id'];
    protected $table = 'admin_venue_schedule';


    public  function  operator()
    {
        return $this->belongsTo(Admin::class,'operator_id','id')
                    ->select(['id','name','picture','email','phone']);
    }


    public  function  venues()
    {
        return $this->belongsTo(Venue::class,'venue_id','id')
                    ->select(['id','name','logo']);
    }
    
    /**
     * 校验课程表设置时间是有效
     * @param array $params
     * @author Red-Bo
     */
    public  function  checkScheduleTimeValidity(array $params)
    {
        $venue_id    = $params['venue_id'];
        $id          = $params['id'];
        $start_time  = date("Y-m-h 00:00:00",strtotime($params['date_between'][0]));
        $end_time    = date("Y-m-h 23:59:59",strtotime($params['date_between'][1]));
        $where = $orWhere1 = $orWhere2 = $orWhere3 = $orWhere4  = [];
        $where[] = ['venue_id','=', $venue_id];
        if($id)
        {
            $where[] = ['id','!=', $id];
        }
        
        $orWhere1 = [
            ['start_time', '>=',$start_time],
            ['start_time' , '<=' , $start_time],
        ];
        $orWhere2 = [
            ['end_time' , '>=', $start_time],
            ['end_time', '<=' , $end_time]
        ];
        $orWhere3 = [
            ['start_time','>=', $start_time],
            ['end_time','<=', $start_time],
        ];
        $orWhere4 = [
            ['start_time','>=', $end_time],
            ['end_time','<=', $end_time],
        ];
        $query = $this->query();
        if($where)
        {
            foreach ($where as $v)
            {
                $query->where($v[0], $v[1], $v[2]);
            }
        }
        
        $query->where(function($query1) use ($orWhere1, $orWhere2, $orWhere3, $orWhere4)
        {
            if($orWhere1)
            {
                $query1->orWhere(function($q2) use ($orWhere1)
                {
                    foreach ($orWhere1 as $v)
                    {
                        $q2->where($v[0], $v[1], $v[2]);
                    }
                });
            }
            
            if($orWhere2)
            {
                $query1->orWhere(function($q3) use ($orWhere2) {
    
                    foreach ($orWhere2 as $v)
                    {
                        $q3->where($v[0], $v[1], $v[2]);
                    }
                });
            }
            
            $query1->orWhere(function($query3) use ($orWhere3, $orWhere4)
            {
               if($orWhere3)
               {
                   $query3->where(function ($q4) use ($orWhere3){
                       foreach ($orWhere3 as $v) {
                         $q4->where($v[0], $v[1], $v[2]);
                       }
                   });
               }
    
                if($orWhere4)
                {
                    $query3->where(function ($q5) use ($orWhere4){
                        foreach ($orWhere4 as $v) {
                            $q5->where($v[0], $v[1], $v[2]);
                        }
                    });
                }
            });
        }) ;
        return $query->get()->toArray();
    }
}
