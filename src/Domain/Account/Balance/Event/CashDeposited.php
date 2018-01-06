<?php

namespace App\Domain\Account\Balance\Event;

use Money\Money;

class CashDeposited extends BalanceEvent
{
    /**
     * @var Money
     */
    private $amount;

    /**
     * @param string $id
     * @param Money $amount
     */
    public function __construct(string $id, Money $amount)
    {
        parent::__construct($id);
        $this->amount = $amount;
    }

    /**
     * @return Money
     */
    public function getAmount(): Money
    {
        return $this->amount;
    }
}