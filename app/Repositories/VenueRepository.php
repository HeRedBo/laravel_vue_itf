<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface VenueRepository
 * @package namespace App\Repositories;
 */
interface VenueRepository extends RepositoryInterface
{
  
    public function  VenueList($request);
    
    public  function  createVenueData(array $data);
    
    public  function  updateVenueData(array $data,$id);

    public function getTreeData();
    
    public function  checkVenueName($name, $id);
}
