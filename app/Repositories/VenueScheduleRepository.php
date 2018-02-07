<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface VenueScheduleRepository
 * @package namespace App\Repositories;
 */
interface VenueScheduleRepository extends RepositoryInterface
{
    
    public  function  create(array $data);

}
