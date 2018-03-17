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

    public  function create(array $data);

    public function update(array $data, $id);
}
