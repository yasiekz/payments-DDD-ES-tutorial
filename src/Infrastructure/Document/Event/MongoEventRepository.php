<?php

namespace App\Infrastructure\Document\Event;

use App\Domain\DomainEvent;
use App\Domain\EventSourcingRepository;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Serializer\SerializerInterface;

class MongoEventRepository implements EventSourcingRepository
{
    const SERIALIZATION_FORMAT = 'json';

    /**
     * @var DocumentManager
     */
    private $manager;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param ManagerRegistry $db
     * @param SerializerInterface $serializer
     */
    public function __construct(ManagerRegistry $db, SerializerInterface $serializer)
    {
        $this->manager = $db->getManager();
        $this->serializer = $serializer;
    }

    /**
     * @param DomainEvent $domainEvent
     */
    public function saveEvent(DomainEvent $domainEvent): void
    {
        $event = new Event(
            $domainEvent->getId(),
            get_class($domainEvent),
            $this->serializer->serialize($domainEvent, self::SERIALIZATION_FORMAT)
        );

        $this->manager->persist($event);
        $this->manager->flush();
    }

    /**
     * @param string $id
     * @return \Iterator
     */
    public function loadEvents(string $id): \Iterator
    {
        $events = $this->manager->createQueryBuilder(Event::class)
            ->field('entityId')->equals($id)
            ->getQuery()
            ->execute();

        /** @var Event[] $events */
        foreach ($events as $event) {
            yield $this->serializer->deserialize(
                $event->getDomainEvent(),
                $event->getClassName(),
                self::SERIALIZATION_FORMAT
            );
        }
    }
}