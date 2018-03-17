<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface CardRepository
 * @package namespace App\Repositories;
 */
interface CardRepository extends RepositoryInterface
{
    public function create(array $data);

    public function  update(array $data, $id);
    
    public function checkCardName(array $params);

    public  function  updateStatus($id, $status);

    public  function  getCardLogger($request);
}
