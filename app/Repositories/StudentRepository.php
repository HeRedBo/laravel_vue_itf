<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;
use Illuminate\Http\Request;
use App\Services\Admin\StudentCard;
use App\Services\Admin\VenueBillService;

/**
 * Interface StudentRepository
 * @package namespace App\Repositories;
 */
interface StudentRepository extends RepositoryInterface
{
    /**
     * create a student
     * @param array $data student info
     * @return mixed
     * @author Red-Bo
     */
    public  function createStudent(array $data,StudentCard $studentCard,VenueBillService $bill_service);
    
    public  function  updateStudent(array $data, $id,StudentCard $studentCard);
    
    public  function getRelationOptions();

    public  function  studentList(Request $request);

    public  function  getStudentInfo($student_id,StudentCard $studentCard);

    public  function  getStudentBaseInfo($student_id,StudentCard $studentCard);


    public  function  sign(array $params);

    public  function getSignCalendar(Request $request);

    public function signClassOptions(Request $request);

    
}
