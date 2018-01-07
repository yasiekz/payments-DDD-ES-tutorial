<?php

namespace Tests\App\Domain\Account;

use App\Domain\Account\Balance\AccountBalance;
use App\Domain\Account\Balance\Event\AccountBalanceCreated;
use App\Domain\Account\Balance\Event\CashDeposited;
use App\Domain\Account\Balance\Event\CashWithdrawn;
use App\Domain\Account\Balance\InsufficientAccountBalanceException;
use App\Domain\Account\Balance\NonSameCurrencyException;
use App\Domain\Account\Balance\ZeroValueTransactionException;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class AccountBalanceTest extends TestCase
{
    const BALANCE_START_VALUE = 0;

    /**
     * @var
     */
    private static $id;

    /**
     *
     */
    public function setUp()
    {
        self::$id = (string)Uuid::uuid4();
    }

    public function testCreation()
    {
        $balance = $this->createBalance();
        $this->assertEquals(self::$id, $balance->getId());
        $this->assertSame(self::BALANCE_START_VALUE, $balance->getAmount());
    }

    public function testCashDeposit()
    {
        $balance = $this->createBalance();
        $balance->deposit(Money::PLN(1000));
        $balance->deposit(Money::PLN(100));

        $this->assertSame(1100, $balance->getAmount());
    }

    public function testCashWithdraw()
    {
        $balance = $this->createBalance();
        $balance->deposit(Money::PLN(1000));
        $balance->withdraw(Money::PLN(300));
        $balance->withdraw(Money::PLN(300));
        $this->assertSame(400, $balance->getAmount());
    }

    public function testCantWithdrawMoreThanExist()
    {
        $this->expectException(InsufficientAccountBalanceException::class);
        $balance = $this->createBalance();
        $balance->deposit(Money::PLN(100));
        $balance->withdraw(Money::PLN(200));
    }

    public function testCantWithdrawZeroMoney()
    {
        $this->expectException(ZeroValueTransactionException::class);
        $balance = $this->createBalance();
        $balance->deposit(Money::PLN(100));
        $balance->withdraw(Money::PLN(0));
    }

    public function testCantDepositZeroMoney()
    {
        $this->expectException(ZeroValueTransactionException::class);
        $balance = $this->createBalance();
        $balance->deposit(Money::PLN(0));
    }

    public function testCantDepositOtherCurrency()
    {
        $this->expectException(NonSameCurrencyException::class);
        $balance = $this->createBalance();
        $balance->deposit(Money::EUR(500));
    }

    public function testEventsAreRecorded()
    {
        $balance = $this->createBalance();
        $balance->deposit(Money::PLN(1000));
        $balance->withdraw(Money::PLN(300));
        $balance->withdraw(Money::PLN(300));

        $events = $balance->getRecordedEvents();
        $this->assertEquals(4, count($events));
        $this->assertInstanceOf(AccountBalanceCreated::class, $events[0]);
        $this->assertInstanceOf(CashDeposited::class, $events[1]);
        $this->assertInstanceOf(CashWithdrawn::class, $events[2]);
        $this->assertInstanceOf(CashWithdrawn::class, $events[3]);
    }

    /**
     * @return AccountBalance
     */
    private function createBalance()
    {
        return AccountBalance::create(self::$id);
    }
}
