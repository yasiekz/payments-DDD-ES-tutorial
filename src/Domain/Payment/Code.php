<?php

namespace App\Domain\Payment;

class Code
{
    const CODE_SALT = 'some_bad_salt';

    /**
     * @var string
     */
    private $code;

    /**
     * @param int $code
     */
    public function __construct(int $code)
    {
        $this->code = self::generateCode($code);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $number
     * @return string
     */
    public static function generateCode(int $number): string
    {
        return md5($number.self::CODE_SALT);
    }

    /**
     * @param Code $code
     * @return bool
     */
    public function equals(Code $code): bool
    {
        return $this->code === $code->getCode();
    }
}