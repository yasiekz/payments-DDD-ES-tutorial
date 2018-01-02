<?php

namespace App\Domain\Payment;

use App\Domain\Account\Balance\AccountBalance;
use App\Domain\Account\Balance\Repository\AccountBalanceReader;
use App\Domain\Account\Balance\Repository\AccountBalanceWriter;
use App\Domain\EventSourcingRepository;
use App\Domain\Payment\Event\PaymentConfirmed;

class PaymentRepository implements AccountBalanceWriter
{
    /**
     * @var AccountBalanceReader
     */
    private $accountBalanceReadRepository;

    /**
     * @var EventSourcingRepository
     */
    private $eventRepository;

    /**
     * @param AccountBalanceReader $accountBalanceReadRepository
     * @param EventSourcingRepository $eventRepository
     */
    public function __construct(
        AccountBalanceReader $accountBalanceReadRepository,
        EventSourcingRepository $eventRepository
    ) {
        $this->accountBalanceReadRepository = $accountBalanceReadRepository;
        $this->eventRepository = $eventRepository;
    }

    /**
     * @param Payment $payment
     */
    public function save(Payment $payment)
    {
        $events = $payment->getRecordedEvents();

        foreach ($events as $event) {
            if ($event instanceof PaymentConfirmed) {
                $this->confirm($payment);
            }
            $this->eventRepository->saveEvent($event);
        }
    }


    /**
     * @inheritdoc
     */
    public function saveBalance(AccountBalance $balance): void
    {
        $events = $balance->getRecordedEvents();

        foreach ($events as $event) {
            $this->eventRepository->saveEvent($event);
        }
    }

    /**
     * @param Payment $payment
     */
    private function confirm(Payment $payment)
    {
        $accountFrom = $this->accountBalanceReadRepository->getById($payment->getAccountFrom());
        $accountTo = $this->accountBalanceReadRepository->getById($payment->getAccountTo());

        $accountFrom->withdraw($payment->getAmount());
        $accountTo->deposit($payment->getAmount());

        $this->saveBalance($accountFrom);
        $this->saveBalance($accountTo);
    }
}
