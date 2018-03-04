<?php

namespace App\Models\Admin;

use Illuminate\Support\Facades\DB;
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
        $id          = isset($params['id']) ? $params['id'] : 0;
        $start_time  = date("Y-m-d 00:00:00",strtotime($params['date_between'][0]));
        $end_time    = date("Y-m-d 23:59:59",strtotime($params['date_between'][1]));
        $where       = $orWhere1 = $orWhere2 = $orWhere3 = $orWhere4 = $orWhere5 = $orWhere6=  [];

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

        $orWhere2_2 = [
            ['start_time' , '<=', $end_time],
            ['end_time', '>=' , $end_time]
        ];

        $orWhere3 = [
            ['start_time','>=', $start_time],
            ['end_time','<=', $start_time],
        ];
        $orWhere4 = [
            ['start_time','>=', $end_time],
            ['end_time','<=', $end_time],
        ];

        $orWhere5 = [
            ['start_time','<=', $start_time],
            ['end_time','>=', $start_time],
        ];
        $orWhere6 = [
            ['start_time','<=', $end_time],
            ['end_time','>=', $end_time],
        ];

        $query = $this->query();
        if($where)
        {
            foreach ($where as $v)
            {
                $query->where($v[0], $v[1], $v[2]);
            }
        }

        $query->where(function($query1) use ($orWhere1, $orWhere2,$orWhere2_2, $orWhere3, $orWhere4,$orWhere5, $orWhere6)
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

            if($orWhere2_2)
            {
                $query1->orWhere(function($q3_2) use ($orWhere2_2) {

                    foreach ($orWhere2_2 as $v)
                    {
                        $q3_2->where($v[0], $v[1], $v[2]);
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

            $query1->orWhere(function($query4) use ($orWhere5, $orWhere6)
            {
                if($orWhere5)
                {
                    $query4->where(function ($q6) use ($orWhere5){
                        foreach ($orWhere5 as $v) {
                            $q6->where($v[0], $v[1], $v[2]);
                        }
                    });
                }

                if($orWhere6)
                {
                    $query4->where(function ($q7) use ($orWhere6){
                        foreach ($orWhere6 as $v) {
                            $q7->where($v[0], $v[1], $v[2]);
                        }
                    });
                }
            });

        }) ;
        return $query->get()->toArray();
    }

    /**
     * 查询某个区间的课程表
     *
     * @param array $params
     * @return array
     */
    public  function getDateBetweenSchedule(array $params)
    {
        $venue_id    = $params['venue_id'];
        $id          = isset($params['id']) ? $params['id'] : 0;
        $start_time  = $params['start_time'];
        $end_time    = $params['end_time'];
        
        $where = $orWhere1 = $orWhere2 = $orWhere2_2 =  $orWhere3  = $orWhere4 = $orWhere5 = $orWhere6=  [];
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
        $orWhere2_2 = [
            ['start_time' , '<=', $end_time],
            ['end_time', '>=' , $end_time]
        ];
        $orWhere3 = [
            ['start_time','>=', $start_time],
            ['end_time','<=', $start_time],
        ];
        $orWhere4 = [
            ['start_time','>=', $end_time],
            ['end_time','<=', $end_time],
        ];

        $orWhere5 = [
            ['start_time','<=', $start_time],
            ['end_time','>=', $start_time],
        ];
        $orWhere6 = [
            ['start_time','<=', $end_time],
            ['end_time','>=', $end_time],
        ];
        $query = $this->query();
        if($where)
        {
            foreach ($where as $v)
            {
                $query->where($v[0], $v[1], $v[2]);
            }
        }

        $query->where(function($query1) use ($orWhere1,  $orWhere2,$orWhere2_2, $orWhere3, $orWhere4, $orWhere5, $orWhere6)
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

            if($orWhere2_2)
            {
                $query1->orWhere(function($q3_2) use ($orWhere2_2) {

                    foreach ($orWhere2_2 as $v)
                    {
                        $q3_2->where($v[0], $v[1], $v[2]);
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

            $query1->orWhere(function($query4) use ($orWhere5, $orWhere6)
            {
                if($orWhere5)
                {
                    $query4->where(function ($q6) use ($orWhere5){
                        foreach ($orWhere5 as $v) {
                            $q6->where($v[0], $v[1], $v[2]);
                        }
                    });
                }

                if($orWhere6)
                {
                    $query4->where(function ($q7) use ($orWhere6){
                        foreach ($orWhere6 as $v) {
                            $q7->where($v[0], $v[1], $v[2]);
                        }
                    });
                }
            });
        }) ;
        return $query->get()->toArray();
    }
}
