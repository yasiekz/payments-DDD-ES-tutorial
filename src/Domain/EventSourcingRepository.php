<?php

namespace App\Domain;

interface EventSourcingRepository
{
    /**
     * @param DomainEvent $event
     */
    public function saveEvent(DomainEvent $event): void;
}