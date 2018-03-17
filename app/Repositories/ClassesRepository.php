<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ClassesRepository
 * @package namespace App\Repositories;
 */
interface ClassesRepository extends RepositoryInterface
{

    public  function create(array  $data);

    public  function  update(array $data, $id);
    
    public  function  delete($id);
    
    public function checkClassName(array $params);
    
    public function getVenueClassOptions($venue_id);
}
