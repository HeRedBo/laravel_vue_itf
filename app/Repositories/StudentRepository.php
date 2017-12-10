<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;
use Illuminate\Http\Request;
use App\Services\Admin\StudentCard;
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
    public  function createStudent(array $data,StudentCard $studentCard);
    
    public  function  updateStudent(array $data, $id);
    
    public  function getRelationOptions();
    
    public  function  getStudentInfo($student_id);

    public  function  studentList(Request $request);
    
}
