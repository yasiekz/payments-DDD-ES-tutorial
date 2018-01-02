<?php

namespace App\Domain\Account\Balance\Repository;

use App\Domain\Account\Balance\AccountBalance;

interface AccountBalanceWriter
{
    /**
     * @param AccountBalance $balance
     */
    public function saveBalance(AccountBalance $balance): void;
}