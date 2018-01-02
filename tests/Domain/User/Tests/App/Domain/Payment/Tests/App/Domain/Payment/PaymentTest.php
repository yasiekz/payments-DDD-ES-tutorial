<?php

namespace Tests\App\Domain\Payment;

use App\Domain\CanNotChangeStateException;
use App\Domain\Payment\Event\PaymentCanceled;
use App\Domain\Payment\Event\PaymentConfirmed;
use App\Domain\Payment\Event\PaymentCreated;
use App\Domain\Payment\Event\UnconfirmedCode;
use App\Domain\Payment\Payment;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class PaymentTest extends TestCase
{
    const CODE_VALUE = 123456;
    const WRONG_CODE_VALUE = 111111;
    const HASHED_CODE_VALUE = 'bd326529ece8623a1ce9f6c838fb49dc';

    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $accountFrom;
    /**
     * @var string
     */
    private $accountTo;
    /**
     * @var Money
     */
    private $amount100;

    public function setUp()
    {
        $this->id = (string)Uuid::uuid4();
        $this->accountFrom = (string)Uuid::uuid4();
        $this->accountTo = (string)Uuid::uuid4();
        $this->amount100 = Money::PLN(100);
    }

    public function testCreate()
    {
        $payment = $this->getFreshPayment();

        $this->assertEquals($this->id, $payment->getId());
        $this->assertEquals($this->accountFrom, $payment->getAccountFrom());
        $this->assertEquals($this->accountTo, $payment->getAccountTo());
        $this->assertEquals($this->amount100, $payment->getAmount());
        $this->assertEquals(self::HASHED_CODE_VALUE, $payment->getCode());
        $this->assertEquals(Payment::STATUS_STARTED, $payment->getStatus());
    }

    public function testConfirm()
    {
        $payment = $this->getFreshPayment();
        $payment->confirm(self::CODE_VALUE);

        $events = $payment->getRecordedEvents();
        $this->assertEquals(Payment::STATUS_CONFIRMED, $payment->getStatus());
        $this->assertEquals(2, count($events));
        $this->assertInstanceOf(PaymentCreated::class, $events[0]);
        $this->assertInstanceOf(PaymentConfirmed::class, $events[1]);
    }

    public function testCancel()
    {
        $payment = $this->getFreshPayment();
        $payment->cancel();

        $events = $payment->getRecordedEvents();
        $this->assertEquals(Payment::STATUS_CANCELED, $payment->getStatus());
        $this->assertEquals(2, count($events));
        $this->assertInstanceOf(PaymentCreated::class, $events[0]);
        $this->assertInstanceOf(PaymentCanceled::class, $events[1]);
    }

    public function testConfirmWithWrongCode()
    {
        $payment = $this->getFreshPayment();
        $payment->confirm(self::WRONG_CODE_VALUE);

        $events = $payment->getRecordedEvents();
        $this->assertEquals(Payment::STATUS_INCORRECT, $payment->getStatus());
        $this->assertEquals(2, count($events));
        $this->assertInstanceOf(PaymentCreated::class, $events[0]);
        $this->assertInstanceOf(UnconfirmedCode::class, $events[1]);
    }

    public function testCantConfirmNotStartedPayment()
    {
        $this->expectException(CanNotChangeStateException::class);
        $payment = $this->getFreshPayment();
        $payment->cancel();

        $payment->confirm(self::CODE_VALUE);

    }

    public function testCantCancelNotStartedPayment()
    {
        $this->expectException(CanNotChangeStateException::class);
        $payment = $this->getFreshPayment();
        $payment->confirm(self::CODE_VALUE);

        $payment->cancel();
    }

    /**
     * @return Payment
     */
    private function getFreshPayment(): Payment
    {
        return Payment::create($this->id, $this->accountFrom, $this->accountTo, $this->amount100, self::CODE_VALUE);
    }
}
