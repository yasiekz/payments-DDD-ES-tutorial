<?php

namespace App\Domain\Payment;

use App\Domain\AggregateRoot;
use App\Domain\CanNotChangeStateException;
use App\Domain\DomainEvent;
use App\Domain\EventNotSupportedException;
use App\Domain\Payment\Event\PaymentCanceled;
use App\Domain\Payment\Event\PaymentConfirmed;
use App\Domain\Payment\Event\PaymentCreated;
use App\Domain\Payment\Event\UnconfirmedCode;
use Money\Money;

class Payment extends AggregateRoot
{
    const STATUS_STARTED = 'STARTED';
    const STATUS_CONFIRMED = 'CONFIRMED';
    const STATUS_CANCELED = 'CANCELED';
    const STATUS_INCORRECT = 'INCORRECT';

    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $accountFrom;
    /**
     * @var string
     */
    private $accountTo;
    /**
     * @var Money
     */
    private $amount;
    /**
     * @var Code
     */
    private $code;
    /**
     * @var string
     */
    private $status;

    /**
     * @param string $id
     * @param string $accountFrom
     * @param string $accountTo
     * @param Money $amount
     * @param int $code
     * @return Payment
     */
    public static function create(string $id, string $accountFrom, string $accountTo, Money $amount, int $code)
    {
        $code = new Code($code);
        $event = new PaymentCreated($id, $accountFrom, $accountTo, $amount, $code);

        $instance = new self();
        $instance->recordEvent($event);

        return $instance;
    }

    /**
     * @param int $code
     * @throws CanNotChangeStateException
     */
    public function confirm(int $code)
    {
        if ($this->status != self::STATUS_STARTED) {
            throw new CanNotChangeStateException('Can only confirm started payments');
        }

        $codeConfirmation = new Code($code);

        if ($this->code->equals($codeConfirmation)) {
            $event = new PaymentConfirmed($this->id);
        } else {
            $event = new UnconfirmedCode($this->id, $codeConfirmation->getCode());
        }

        $this->recordEvent($event);
    }

    /**
     * @throws CanNotChangeStateException
     */
    public function cancel()
    {
        if ($this->status != self::STATUS_STARTED) {
            throw new CanNotChangeStateException('Can only confirm started payments');
        }

        $event = new PaymentCanceled($this->id);

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
     * @return string
     */
    public function getAccountFrom(): string
    {
        return $this->accountFrom;
    }

    /**
     * @return string
     */
    public function getAccountTo(): string
    {
        return $this->accountTo;
    }

    /**
     * @return Money
     */
    public function getAmount(): Money
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code->getCode();
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param DomainEvent $event
     * @throws EventNotSupportedException
     */
    protected function applyEvent(DomainEvent $event): void
    {
        switch (true) {
            case $event instanceof PaymentCreated:
                $this->id = $event->getId();
                $this->accountFrom = $event->getAccountFrom();
                $this->accountTo = $event->getAccountTo();
                $this->amount = $event->getAmount();
                $this->code = $event->getCode();
                $this->status = self::STATUS_STARTED;
                break;
            case $event instanceof PaymentConfirmed:
                $this->status = self::STATUS_CONFIRMED;
                break;
            case $event instanceof PaymentCanceled:
                $this->status = self::STATUS_CANCELED;
                break;
            case $event instanceof UnconfirmedCode:
                $this->status = self::STATUS_INCORRECT;
                break;
            default:
                throw new EventNotSupportedException('Event '.get_class($event).' is not supported');
        }
    }

}