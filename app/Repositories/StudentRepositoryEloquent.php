<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\StudentRepository;
use App\Models\Admin\Student;
use App\Models\Admin\RelationName;

/**
 * Class StudentRepositoryEloquent
 * @package namespace App\Repositories;
 */
class StudentRepositoryEloquent extends BaseRepository implements StudentRepository
{
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


    public  function  getRelationOptions()
    {
        return  RelationName::all(['id','name'])->toArray();
    }
}
