<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface AdminRepository
 * @package namespace App\Repositories;
 */
interface AdminRepository extends RepositoryInterface
{
    
    public function createAdminData(array $data);
    
    public  function getAdminInfo($id);
    
    public  function updateAdminData($id , array $data);

}
