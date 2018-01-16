<?php

namespace App\DataFixtures\MongoDB;

use App\Domain\Account\Balance\Event\AccountBalanceCreated;
use App\Infrastructure\Document\Event\Event;
use App\Infrastructure\Document\Event\MongoEventRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Money\Money;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BalanceFixtures extends Fixture implements ContainerAwareInterface
{
    const BALANCE1_UUID = 'e6e90e7f-74ea-4e61-a675-ad58782e1b5e';
    const BALANCE2_UUID = '3eb1c207-ff97-448b-91ed-6352a5346a76';
    const BALANCE3_UUID = '671c31d8-ada3-4be3-84a4-ee5f5a128739';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private static $balances;

    public function __construct()
    {
        self::$balances = [
            self::BALANCE1_UUID => Money::PLN(100000),
            self::BALANCE2_UUID => Money::PLN(20000),
            self::BALANCE3_UUID => Money::PLN(0),
        ];
    }

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::$balances as $uuid => $balance) {
            $domainEvent = new AccountBalanceCreated($uuid, $balance);

            $event = new Event(
                $uuid,
                get_class($domainEvent),
                $this->container->get('serializer')->serialize($domainEvent, MongoEventRepository::SERIALIZATION_FORMAT)
            );
            $manager->persist($event);
        }

        $manager->flush();
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}