<?php

namespace App\Domain;

interface EventSourcingRepository
{
    /**
     * @param DomainEvent $event
     */
    public function saveEvent(DomainEvent $event): void;

    /**
     * @param string $id
     * @return \Iterator
     */
    public function loadEvents(string $id): \Iterator;
}