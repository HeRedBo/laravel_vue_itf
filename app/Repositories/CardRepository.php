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
    
    public function checkCardName($name,$id);
}
