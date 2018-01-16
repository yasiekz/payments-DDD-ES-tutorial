<?php

namespace App\Domain\Account\Balance\Repository;

use App\Domain\Account\Balance\AccountBalance;
use App\Domain\EntityNotFoundException;
use App\Domain\EventSourcingRepository;

class AccountBalanceRepository implements AccountBalanceReader, AccountBalanceWriter
{
    /**
     * @var EventSourcingRepository
     */
    private $eventRepository;

    /**
     * AccountBalanceRepository constructor.
     * @param EventSourcingRepository $eventRepository
     */
    public function __construct(EventSourcingRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @param string $id
     * @return AccountBalance
     * @throws EntityNotFoundException
     */
    public function getById(string $id): AccountBalance
    {
        $events = $this->eventRepository->loadEvents($id);

        return AccountBalance::regenerateFromEvents($events);
    }

    /**
     * @param AccountBalance $balance
     */
    public function saveBalance(AccountBalance $balance): void
    {
        $events = $balance->getRecordedEvents();

        foreach ($events as $event) {
            $this->eventRepository->saveEvent($event);
        }
    }
}