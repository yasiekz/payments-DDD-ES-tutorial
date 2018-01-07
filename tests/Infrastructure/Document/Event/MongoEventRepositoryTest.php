<?php

namespace App\Tests\Infrastructure\Document\Event;

use App\Domain\Account\Balance\Event\CashDeposited;
use App\Domain\Account\Balance\Event\CashWithdrawn;
use App\Domain\DomainEvent;
use App\Domain\Payment\Code;
use App\Domain\Payment\Event\PaymentConfirmed;
use App\Domain\Payment\Event\PaymentCreated;
use App\Infrastructure\Document\Event\Event;
use App\Infrastructure\Document\Event\MongoEventRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MongoEventRepositoryTest extends KernelTestCase
{
    /**
     * @var ContainerInterface
     */
    private static $container;
    /**
     * @var MongoEventRepository
     */
    private $repository;

    public function setUp()
    {
        self::bootKernel();
        self::$container = static::$kernel->getContainer();
        $db = self::$container->get('doctrine_mongodb');
        $serializer = self::$container->get('serializer');

        $this->repository = new MongoEventRepository($db, $serializer);
    }

    public function tearDown()
    {
        // clean the db
        $db = self::$container->get('doctrine_mongodb');
        /** @var DocumentManager $documentManager */
        $documentManager = $db->getManager();
        $documentManager->getSchemaManager()->dropDocumentCollection(Event::class);
    }

    public function testSaveAndLoad()
    {
        $uuid = (string)Uuid::uuid4();
        $accountBalanceFrom = (string)Uuid::uuid4();
        $accountBalanceTo = (string)Uuid::uuid4();
        $money = Money::PLN(100);
        $code = Code::generate(123456);

        $paymentCreated = new PaymentCreated($uuid, $accountBalanceFrom, $accountBalanceTo, $money, $code);
        $paymentConfirmed = new PaymentConfirmed($uuid);
        $cashWithdrawn = new CashWithdrawn($accountBalanceFrom, $money);
        $cashDeposited = new CashDeposited($accountBalanceTo, $money);

        $this->repository->saveEvent($paymentCreated);
        $this->repository->saveEvent($paymentConfirmed);
        $this->repository->saveEvent($cashWithdrawn);
        $this->repository->saveEvent($cashDeposited);

        $events = $this->repository->loadEvents($uuid);
        /** @var DomainEvent[] $eventsArray */
        $eventsArray = iterator_to_array($events);

        $this->assertEquals(2, count($eventsArray));
        $this->assertEquals($paymentCreated, $eventsArray[0]);
        $this->assertEquals($paymentConfirmed, $eventsArray[1]);

        $balanceFromEvents = $this->repository->loadEvents($accountBalanceFrom);
        $this->assertEquals($cashWithdrawn, $balanceFromEvents->current());

        $balanceToEvents = $this->repository->loadEvents($accountBalanceTo);
        $this->assertEquals($cashDeposited, $balanceToEvents->current());
    }
}
