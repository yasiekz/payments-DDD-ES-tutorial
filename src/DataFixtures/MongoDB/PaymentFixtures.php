<?php

namespace App\DataFixtures\MongoDB;

use App\Domain\DomainEvent;
use App\Domain\Payment\Code;
use App\Domain\Payment\Event\PaymentConfirmed;
use App\Domain\Payment\Event\PaymentCreated;
use App\Infrastructure\Document\Event\Event;
use App\Infrastructure\Document\Event\MongoEventRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Money\Money;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @codeCoverageIgnore
 */
class PaymentFixtures extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    const PAYMENT1_UUID = '0e1fd931-de13-4d47-82af-7e1f97081d61';
    const PAYMENT2_UUID = 'eefcae84-549f-46cb-9f1d-2daaaf981055';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $payment1Events = [
            new PaymentCreated(
                self::PAYMENT1_UUID,
                BalanceFixtures::BALANCE1_UUID,
                BalanceFixtures::BALANCE3_UUID,
                Money::PLN(3000),
                Code::generate(123456)
            ),
            new PaymentConfirmed(self::PAYMENT1_UUID),
        ];

        $payment2Events = [
            new PaymentCreated(
                self::PAYMENT2_UUID,
                BalanceFixtures::BALANCE2_UUID,
                BalanceFixtures::BALANCE1_UUID,
                Money::PLN(3000),
                Code::generate(123456)
            ),
            new PaymentConfirmed(self::PAYMENT2_UUID),
        ];

        $events = array_merge($payment1Events, $payment2Events);

        /** @var DomainEvent $domainEvent */
        foreach ($events as $domainEvent) {
            $event = new Event(
                $domainEvent->getId(),
                get_class($domainEvent),
                $this->container->get('serializer')->serialize($domainEvent, MongoEventRepository::SERIALIZATION_FORMAT)
            );

            $manager->persist($event);
        }

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    function getDependencies()
    {
        return [
            BalanceFixtures::class,
        ];
    }

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}