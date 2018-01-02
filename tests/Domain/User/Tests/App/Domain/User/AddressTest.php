<?php

namespace Tests\App\Domain\User;

use App\Domain\User\Address;
use App\Domain\User\PostCode;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    const POSTCODE_VALUE = '32-436';
    const STREET_VALUE = 'street';
    const POST_VALUE = 'post';
    const CITY_VALUE = 'city';

    public function testObject()
    {
        $postCode = $this->getMockBuilder(PostCode::class)->disableOriginalConstructor()->getMock();
        $postCode->expects($this->any())->method('getCode')->willReturn(self::POSTCODE_VALUE);
        $address = new Address(self::STREET_VALUE, self::POST_VALUE, $postCode, self::CITY_VALUE);

        $this->assertSame(self::CITY_VALUE, $address->getCity());
        $this->assertSame(self::STREET_VALUE, $address->getStreet());
        $this->assertSame(self::POST_VALUE, $address->getPost());
        $this->assertSame(self::POSTCODE_VALUE, $address->getPostCode());
    }
}
