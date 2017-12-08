<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\studentNumberCardRepository;
use App\Models\Admin\StudentNumberCard;
use App\Validators\StudentNumberCardValidator;

/**
 * Class StudentNumberCardRepositoryEloquent
 * @package namespace App\Repositories;
 */
class StudentNumberCardRepositoryEloquent extends BaseRepository implements StudentNumberCardRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return StudentNumberCard::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
