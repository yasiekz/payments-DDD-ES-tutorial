<?php

namespace App\Domain\Account\Balance\Event;

use Money\Money;
use Ramsey\Uuid\Uuid;

class CashWithdrawn extends BalanceEvent
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