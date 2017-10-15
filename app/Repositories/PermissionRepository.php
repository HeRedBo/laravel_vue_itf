<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface PermissionRepository
 * @package namespace App\Repositories;
 */
interface PermissionRepository extends RepositoryInterface
{
    
   	public  function  getSelectList();
    public  function  getTreeData();

    public  function createPermissionData(array $data);

    public function updatePermissionData(array $data, $id);
}
