<?php

namespace App\Infrastructure\Document\Event;

use MongoDB\BSON\ObjectID;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document()
 */
class Event
{
    /**
     * @var ObjectID
     * @MongoDB\Id()
     */
    private $id;
    /**
     * @var string
     * @MongoDB\Field(type="string")
     */
    private $entityId;
    /**
     * @var string
     * @MongoDB\Field(type="string")
     */
    private $domainEvent;
    /**
     * @var string
     * @MongoDB\Field(type="string")
     */
    private $className;
    /**
     * @var \DateTime
     * @MongoDB\Field(type="date")
     */
    private $createdAt;

    /**
     * @param string $entityId
     * @param string $className
     * @param string $domainEvent
     */
    public function __construct(string $entityId, string $className, string $domainEvent)
    {
        $this->entityId = $entityId;
        $this->domainEvent = $domainEvent;
        $this->createdAt = new \DateTime();
        $this->className = $className;
    }

    /**
     * @return ObjectID
     */
    public function getId(): ObjectID
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEntityId(): string
    {
        return $this->entityId;
    }

    /**
     * @return string
     */
    public function getDomainEvent(): string
    {
        return $this->domainEvent;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }
}