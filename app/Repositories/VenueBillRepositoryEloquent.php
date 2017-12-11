<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\venueBillRepository;
use App\Models\Admin\VenueBill;
use App\Validators\VenueBillValidator;

/**
 * Class VenueBillRepositoryEloquent
 * @package namespace App\Repositories;
 */
class VenueBillRepositoryEloquent extends BaseRepository implements VenueBillRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return VenueBill::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
