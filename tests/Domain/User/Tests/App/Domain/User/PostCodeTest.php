<?php

namespace Tests\App\Domain\User;

use App\Domain\User\PostCode;
use App\Domain\User\PostCodeNotSupportedException;
use PHPUnit\Framework\TestCase;

class PostCodeTest extends TestCase
{
    /**
     * @param $code
     * @dataProvider provideGood
     */
    public function testOk($code)
    {
        $postCode = new PostCode($code);
        $this->assertEquals($code, $postCode->getCode());
    }

    /**
     * @param $code
     * @dataProvider provideWrong
     */
    public function testFail($code)
    {
        $this->expectException(PostCodeNotSupportedException::class);
        new PostCode($code);
    }

    /**
     * @return array
     */
    public function provideGood()
    {
        return [
            ['11-211'],
            ['01-234'],
            ['56-789'],
        ];
    }

    /**
     * @return array
     */
    public function provideWrong()
    {
        return [
            ['11-1111'],
            ['111-111'],
            ['11-11'],
            ['ggg-aa'],
            ['Ggg-Aa'],
            ['111-GD'],
        ];
    }
}
