<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface CardRepository
 * @package namespace App\Repositories;
 */
interface CardRepository extends RepositoryInterface
{
    public function createCard(array $data);

    public function  updateCard(array $data, $id);
    
    public function checkCardName($name,$id);

    public  function  updateStatus($id, $status);

    public  function  getCardLogger($request);
}
