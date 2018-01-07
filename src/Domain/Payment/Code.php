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
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $number
     * @return Code
     */
    public static function generate(int $number): Code
    {
        $instance = new self();
        $instance->code = md5($number.self::CODE_SALT);

        return $instance;
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