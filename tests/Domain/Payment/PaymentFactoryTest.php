<?php

namespace Tests\App\Domain\Payment;

use App\Domain\Account\Balance\AccountBalance;
use App\Domain\Account\Balance\InsufficientAccountBalanceException;
use App\Domain\Account\Balance\Repository\AccountBalanceReader;
use App\Domain\Payment\Payment;
use App\Domain\Payment\PaymentFactory;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class PaymentFactoryTest extends TestCase
{
    /**
     * @var string
     */
    private $idFrom;
    /**
     * @var string
     */
    private $idTo;
    /**
     * @var PaymentFactory
     */
    private $factory;

    public function setUp()
    {
        $this->idFrom = (string)Uuid::uuid4();
        $this->idTo = (string)Uuid::uuid4();

        $balanceFrom = $this->createMock(AccountBalance::class);
        $balanceFrom->method('getAmount')->willReturn(1000);

        $balanceTo = $this->createMock(AccountBalance::class);

        $repository = $this->createMock(AccountBalanceReader::class);
        $repository->expects($this->any())->method('getById')->withConsecutive(
            $this->equalTo($this->idFrom),
            $this->equalTo($this->idTo)
        )->willReturnOnConsecutiveCalls($balanceFrom, $balanceTo);

        $this->factory = new PaymentFactory($repository);
    }

    public function testCreate()
    {
        $payment = $this->factory->create((string)Uuid::uuid4(), $this->idFrom, $this->idTo, Money::PLN(100), 123456);
        $this->assertInstanceOf(Payment::class, $payment);
    }

    public function testCreateFailedInsufficientFunds()
    {
        $this->expectException(InsufficientAccountBalanceException::class);
        $this->factory->create((string)Uuid::uuid4(), $this->idFrom, $this->idTo, Money::PLN(10000), 123456);
    }

}
