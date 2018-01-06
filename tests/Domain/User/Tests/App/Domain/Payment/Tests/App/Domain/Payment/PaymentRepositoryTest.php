<?php

namespace Tests\App\Domain\Payment;

use App\Domain\Account\Balance\AccountBalance;
use App\Domain\Account\Balance\Event\CashDeposited;
use App\Domain\Account\Balance\Event\CashWithdrawn;
use App\Domain\Account\Balance\Repository\AccountBalanceReader;
use App\Domain\DomainEvent;
use App\Domain\EventSourcingRepository;
use App\Domain\Payment\Code;
use App\Domain\Payment\Event\PaymentConfirmed;
use App\Domain\Payment\Event\PaymentCreated;
use App\Domain\Payment\Payment;
use App\Domain\Payment\PaymentRepository;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class PaymentRepositoryTest extends TestCase
{
    /**
     * @var AccountBalanceReader
     */
    private $accountBalanceRepository;
    /**
     * @var string
     */
    private $uuid1;
    /**
     * @var string
     */
    private $uuid2;
    /**
     * @var DomainEvent
     */
    private $balanceEvent1;
    /**
     * @var DomainEvent
     */
    private $balanceEvent2;
    /**
     * @var Money
     */
    private $money;

    public function setUp()
    {
        $this->uuid1 = (string)Uuid::uuid4();
        $this->uuid2 = (string)Uuid::uuid4();

        $this->money = Money::PLN(100);

        $accountBalance1 = $this->createMock(AccountBalance::class);
        $accountBalance1->expects($this->any())->method('getId')->willReturn($this->uuid1);
        $this->balanceEvent1 = new CashWithdrawn($this->uuid1, $this->money);
        $accountBalance1->expects($this->any())->method('getRecordedEvents')->willReturn([$this->balanceEvent1]);

        $accountBalance2 = $this->createMock(AccountBalance::class);
        $accountBalance2->expects($this->any())->method('getId')->willReturn($this->uuid2);
        $this->balanceEvent2 = new CashDeposited($this->uuid2, $this->money);
        $accountBalance2->expects($this->any())->method('getRecordedEvents')->willReturn([$this->balanceEvent2]);

        /** @var \PHPUnit_Framework_MockObject_MockObject|AccountBalanceReader $accountBalanceRepository */
        $this->accountBalanceRepository = $this->getMockBuilder(AccountBalanceReader::class)->getMock();
        $this->accountBalanceRepository->expects($this->any())->method('getById')->withConsecutive(
            $this->equalTo($this->uuid1),
            $this->equalTo($this->uuid2)
        )->willReturnOnConsecutiveCalls($accountBalance1, $accountBalance2);
    }


    public function testNewPayment()
    {
        $paymentId = (string)Uuid::uuid4();

        $event1 = new PaymentCreated($paymentId, $this->uuid1, $this->uuid2, $this->money, Code::generate(123456));
        $event2 = new PaymentConfirmed($paymentId);

        /** @var EventSourcingRepository|\PHPUnit_Framework_MockObject_MockObject $eventSourcingRepository */
        $eventSourcingRepository = $this->createMock(EventSourcingRepository::class);
        $eventSourcingRepository->expects($this->exactly(4))->method('saveEvent')->withConsecutive(
            [$this->equalTo($event1)],
            [$this->equalTo($this->balanceEvent1)],
            [$this->equalTo($this->balanceEvent2)],
            [$this->equalTo($event2)]
        );

        $repository = new PaymentRepository($this->accountBalanceRepository, $eventSourcingRepository);

        $events = [
            $event1,
            $event2,
        ];

        /** @var Payment|\PHPUnit_Framework_MockObject_MockObject $payment */
        $payment = $this->getMockBuilder(Payment::class)->disableOriginalConstructor()->getMock();
        $payment->expects($this->any())->method('getRecordedEvents')->willReturn($events);
        $payment->expects($this->any())->method('getAmount')->willReturn($this->money);

        $repository->save($payment);
    }

    public function testGetById()
    {
        $paymentId = (string)Uuid::uuid4();

        $events = [
            new PaymentCreated($paymentId, $this->uuid1, $this->uuid2, $this->money, Code::generate(123456)),
            new PaymentConfirmed($paymentId),
        ];

        $eventSourcingRepository = $this->createMock(EventSourcingRepository::class);
        $eventSourcingRepository->method('loadEvents')->with($paymentId)->willReturn(new \ArrayIterator($events));

        $repository = new PaymentRepository($this->accountBalanceRepository, $eventSourcingRepository);

        $payment = $repository->getById($paymentId);

        $this->assertEquals(100, (int)$payment->getAmount()->getAmount());
        $this->assertEquals(Payment::STATUS_CONFIRMED, $payment->getStatus());
        $this->assertEquals($this->uuid1, $payment->getAccountFrom());
        $this->assertEquals($this->uuid2, $payment->getAccountTo());
    }
}
