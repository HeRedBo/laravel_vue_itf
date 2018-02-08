<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;
use Illuminate\Http\Request;

/**
 * Interface VenueScheduleRepository
 * @package namespace App\Repositories;
 */
interface VenueScheduleRepository extends RepositoryInterface
{
    
    public  function  index(Request $request);
    
    public  function  create(array $data);
    
    public  function  show($id);
    
    public  function  update(array $data, $id);
    
    public  function  delete($id);
    
    
}
