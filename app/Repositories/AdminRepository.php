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
    
    public  function updateAdminData(array $data, $id);

    public function checkUserName($name, $id);

    public function deleteUser($id);
    
    public  function  getUserVenues($uid);

}
