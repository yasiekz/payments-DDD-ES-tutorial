<?php

namespace App\Domain\Account\Balance\Repository;

use App\Domain\Account\Balance\AccountBalance;
use App\Domain\EntityNotFoundException;

interface AccountBalanceReader
{
    /**
     * @param string $id
     * @return AccountBalance
     * @throws EntityNotFoundException
     */
    public function getById(string $id): AccountBalance;
}