<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\VenueRepository;
use App\Models\Admin\Venue;

/**
 * Class VenueRepositoryEloquent
 * @package namespace App\Repositories;
 */
class VenueRepositoryEloquent extends BaseRepository implements VenueRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Venue::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
