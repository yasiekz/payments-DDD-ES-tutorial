<?php

namespace App\Domain\User;

class PostCode
{
    /**
     * @var
     */
    private $code;

    /**
     * @param $code
     * @throws \Exception
     */
    public function __construct($code)
    {
        if (preg_match('/^[0-9]{2}-[0-9]{3}$/', $code)) {
            $this->code = $code;
        } else {
            throw new PostCodeNotSupportedException('Postcode must be in xx-xxx format');
        }
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}