<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ClassesRepository
 * @package namespace App\Repositories;
 */
interface ClassesRepository extends RepositoryInterface
{

    public  function createClass(array  $data);

    public  function  updateClass(array $data, $id);
    
    public  function  deleteClasses($id);
    
    public function checkClassName($name,$id);
    
    public function getVenueClassOptions($venue_id);
}
