<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

use Illuminate\Http\Request;

/**
 * Interface RoleRepository
 * @package namespace App\Repositories;
 */
interface RoleRepository extends RepositoryInterface
{
    //

    public function getAcl($roleId);

    public function setAcl(Request $request);
}