<?php

namespace App\Domain\Account\Balance;

use Money\Money;

class AccountBalanceAssert
{
    /**
     * @param Money $actualBalance
     * @param Money $money
     * @throws NonSameCurrencyException
     */
    public static function checkCurrency(Money $actualBalance, Money $money)
    {
        if (!$actualBalance->isSameCurrency($money)) {
            throw new NonSameCurrencyException(
                sprintf(
                    'Cant deposit %d %s when account is in %s',
                    $money->getAmount(),
                    $money->getCurrency()->getCode(),
                    $actualBalance->getCurrency()->getCode()
                )
            );
        }
    }

    /**
     * @param Money $actualBalance
     * @param Money $money
     * @throws InsufficientAccountBalanceException
     */
    public static function checkMoneyAmount(Money $actualBalance, Money $money)
    {
        if ($money->greaterThan($actualBalance)) {
            throw new InsufficientAccountBalanceException(
                sprintf(
                    'Cannot withdraw %d when account has only %d',
                    $money->getAmount(),
                    $actualBalance->getAmount()
                )
            );
        }
    }

    /**
     * @param Money $money
     * @throws ZeroValueTransactionException
     */
    public static function checkInputValue(Money $money)
    {
        if ($money->getAmount() == 0) {
            throw new ZeroValueTransactionException('Cant perform transaction with 0 value');
        }
    }
}