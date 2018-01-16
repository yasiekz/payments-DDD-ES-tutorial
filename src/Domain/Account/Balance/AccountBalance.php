<?php

namespace App\Domain\Account\Balance;

use App\Domain\Account\Balance\Event\AccountBalanceCreated;
use App\Domain\Account\Balance\Event\CashDeposited;
use App\Domain\Account\Balance\Event\CashWithdrawn;
use App\Domain\DomainEvent;
use App\Domain\EventNotSupportedException;
use App\Domain\EventSourceTrait;
use Money\Money;

class AccountBalance
{
    use EventSourceTrait;

    /**
     * @var string
     */
    private $id;

    /**
     * @var Money
     */
    private $balance;

    /**
     * @param string $id
     * @param Money $money
     * @return AccountBalance
     */
    public static function create(string $id, Money $money)
    {
        $instance = new self;
        $event = new AccountBalanceCreated($id, $money);
        $instance->recordEvent($event);

        return $instance;
    }

    /**
     * @param Money $money
     */
    public function deposit(Money $money): void
    {
        AccountBalanceAssert::checkCurrency($this->balance, $money);
        AccountBalanceAssert::checkInputValue($money);

        $event = new CashDeposited($this->id, $money);
        $this->recordEvent($event);
    }

    /**
     * @param Money $money
     */
    public function withdraw(Money $money): void
    {
        AccountBalanceAssert::checkCurrency($this->balance, $money);
        AccountBalanceAssert::checkMoneyAmount($this->balance, $money);
        AccountBalanceAssert::checkInputValue($money);

        $event = new CashWithdrawn($this->id, $money);
        $this->recordEvent($event);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return (int)$this->balance->getAmount();
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->balance->getCurrency()->getCode();
    }

    /**
     * @inheritdoc
     */
    protected function applyEvent(DomainEvent $event): void
    {
        // todo: when method become bigger, just put cases into private methods
        switch (true) {
            case $event instanceof AccountBalanceCreated:
                $this->balance = $event->getAmount();
                $this->id = $event->getId();
                break;
            case $event instanceof CashWithdrawn:
                $this->balance = $this->balance->subtract($event->getAmount());
                break;
            case $event instanceof CashDeposited:
                $this->balance = $this->balance->add($event->getAmount());
                break;
            default:
                throw new EventNotSupportedException('Event '.get_class($event).' is not supported');
        }
    }
}