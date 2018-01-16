<?php

namespace App\Tests\Domain\Account\Repository;

use App\Domain\Account\Balance\AccountBalance;
use App\Domain\Account\Balance\Event\AccountBalanceCreated;
use App\Domain\Account\Balance\Event\CashDeposited;
use App\Domain\Account\Balance\Event\CashWithdrawn;
use App\Domain\Account\Balance\Repository\AccountBalanceRepository;
use App\Domain\EventSourcingRepository;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class AccountBalanceRepositoryTest extends TestCase
{
    public function testGetById()
    {
        $id = (string)Uuid::uuid4();

        $events = [
            new AccountBalanceCreated($id, Money::PLN(1000)),
            new CashWithdrawn($id, Money::PLN(300)),
            new CashDeposited($id, Money::PLN(100)),
        ];

        $eventSourcingRepository = $this->createMock(EventSourcingRepository::class);
        $eventSourcingRepository->method('loadEvents')->with($id)->willReturn(new \ArrayIterator($events));

        $repository = new AccountBalanceRepository($eventSourcingRepository);

        $accountBalance = $repository->getById($id);

        $this->assertEquals(800, $accountBalance->getAmount());
        $this->assertEquals('PLN', $accountBalance->getCurrency());
        $this->assertEquals($id, $accountBalance->getId());
    }

    public function testSave()
    {
        $id = (string)Uuid::uuid4();

        $events = [
            new AccountBalanceCreated($id, Money::PLN(1000)),
            new CashWithdrawn($id, Money::PLN(300)),
            new CashDeposited($id, Money::PLN(100)),
        ];

        /** @var \PHPUnit_Framework_MockObject_MockObject|AccountBalance $accountBalance */
        $accountBalance = $this->createMock(AccountBalance::class);
        $accountBalance->method('getRecordedEvents')->willReturn($events);

        $eventSourcingRepository = $this->createMock(EventSourcingRepository::class);
        $eventSourcingRepository->expects($this->exactly(3))->method('saveEvent')->withConsecutive(
            [$this->equalTo($events[0])],
            [$this->equalTo($events[1])],
            [$this->equalTo($events[2])]
        );

        $repository = new AccountBalanceRepository($eventSourcingRepository);

        $repository->saveBalance($accountBalance);
    }
}