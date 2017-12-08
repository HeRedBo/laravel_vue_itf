<?php
/**
 * 学生会员卡服务
 * Created by PhpStorm.
 * User: Red-Bo
 * Date: 2017/12/8 14:15
 * Desc:
 */

namespace App\Services\Admin;

use App\Repositories\StudentNumberCardRepositoryEloquent;
use App\Repositories\VenueRepositoryEloquent;
use App\Repositories\StudentRepositoryEloquent;

class StudentCard
{
    /**
     * @var StudentNumberCardRepositoryEloquent
     */
    protected  $studentNumberCardRepository;
    protected  $venueRepository;
    protected  $studentRepository;
    
    protected  $student_number_card_redis_key = 'student_number_card_redis_key';
    
    public  function __construct(
        StudentNumberCardRepositoryEloquent $studentNumberCardRepository,
        VenueRepositoryEloquent $venueRepository,
        StudentRepositoryEloquent $studentRepository
    )
    {
        $this->studentNumberCardRepository = $studentNumberCardRepository;
        $this->venueRepository             = $venueRepository;
        $this->studentRepository           = $studentRepository;
    }
    
    /**
     * 保存用户会员卡信息
     * @param int $student_id
     * @param array $request
     * @author Red-Bo
     */
    public  function saveStudentNumberCard($student_id, $request)
    {
        
    }
    
    /**
     * 创建学生会员卡编号
     * @param int $student_id  学生ID
     * @author Red-Bo
     */
    public function createUserCardNumber($student_id)
    {
        $student_info = $this->studentRepository->find($student_id);
        if($student_info)
        {
            $venue_id = $student_info->venue_id;
            $venue_info = $this->venueRepository->find($venue_id);
            $card_prefix = $venue_info->card_prefix;
            //学生归属编号
            $student_num = str_pad(substr($student_id,-4), 4, "0", STR_PAD_LEFT);
        }
    }
    
    
    
    
    
    
}