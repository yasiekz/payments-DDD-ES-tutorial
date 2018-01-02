<?php

namespace App\Domain;

trait EventSourceTrait
{
    /**
     * @var DomainEvent[]
     */
    protected $recordedEvents;

    /**
     * @var int
     */
    protected $version = 0;

    /**
     * EventSourcedTrait constructor.
     */
    public final function __construct()
    {
    }

    /**
     * @param \Iterator $events
     * @return static
     */
    public static function regenerateFromEvents(\Iterator $events)
    {
        $instance = new static();

        foreach ($events as $event) {
            $instance->version += 1;
            $instance->applyEvent($event);
        }

        return $instance;
    }

    /**
     * @param DomainEvent $event
     */
    protected function recordEvent(DomainEvent $event)
    {
        $this->version += 1;
        $this->recordedEvents[] = $event;
        $this->applyEvent($event);
    }

    /**
     * @return DomainEvent[]
     */
    public function getRecordedEvents()
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }

    /**
     * @param DomainEvent $event
     * @return mixed
     */
    abstract protected function applyEvent(DomainEvent $event): void;
}