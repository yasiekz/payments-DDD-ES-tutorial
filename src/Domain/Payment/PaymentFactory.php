<?php

namespace App\Domain\Payment;

use App\Domain\Account\Balance\AccountBalanceAssert;
use App\Domain\Account\Balance\InsufficientAccountBalanceException;
use App\Domain\Account\Balance\Repository\AccountBalanceReader;
use Money\Money;

class PaymentFactory
{
    /**
     * @var AccountBalanceReader
     */
    private $accountBalanceReadRepository;

    /**
     * PaymentFactory constructor.
     * @param AccountBalanceReader $accountBalanceReadRepository
     */
    public function __construct(AccountBalanceReader $accountBalanceReadRepository)
    {
        $this->accountBalanceReadRepository = $accountBalanceReadRepository;
    }

    /**
     * @param string $id
     * @param string $accountFrom
     * @param string $accountTo
     * @param Money $amount
     * @param int $code
     * @return Payment
     * @throws InsufficientAccountBalanceException
     */
    public function create(string $id, string $accountFrom, string $accountTo, Money $amount, int $code)
    {
        $balanceFrom = $this->accountBalanceReadRepository->getById($accountFrom);
        // we need to check that this account exists, otherwise EntityNotFoundException will be thrown
        $this->accountBalanceReadRepository->getById($accountTo);

        if ($balanceFrom->getAmount() > (int)$amount->getAmount()) {
            return Payment::create($id, $accountFrom, $accountTo, $amount, $code);
        }

        throw new InsufficientAccountBalanceException('Insufficient account balance');
    }
}