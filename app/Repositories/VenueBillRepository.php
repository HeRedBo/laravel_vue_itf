<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface VenueBillRepository
 * @package namespace App\Repositories;
 */
interface VenueBillRepository extends RepositoryInterface
{
    
    /**
     * 保存账单数据类型字典
     * @param  object  $request
     * @return 
     */
    public function saveVenueBillDataType($request);
    
}
