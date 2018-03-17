<?php

namespace App\Repositories;

use Illuminate\Http\Request;
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


    public function logger(Request $request);

    public  function  getUserList(Request $request);

    public  function  user();

}
