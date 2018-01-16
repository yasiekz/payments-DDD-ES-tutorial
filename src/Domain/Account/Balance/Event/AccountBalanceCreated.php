<?php

namespace App\Domain\Account\Balance\Event;

use Money\Money;

class AccountBalanceCreated extends BalanceEvent
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
        $this->amount = $amount;
        parent::__construct($id);
    }

    /**
     * @return Money
     */
    public function getAmount(): Money
    {
        return $this->amount;
    }
}