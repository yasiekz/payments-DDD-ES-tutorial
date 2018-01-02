<?php

namespace Tests\App\Domain\User;

use App\Domain\User\Address;
use App\Domain\User\User;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;


/**
 * @coversDefaultClass App\Domain\User\User
 */
class UserTest extends TestCase
{
    const NAME = 'John Doe';
    const POSTCODE_VALUE = '32-436';
    const STREET_VALUE = 'street';
    const POST_VALUE = 'post';
    const CITY_VALUE = 'city';

    private $id;

    public function setUp()
    {
        $this->id = (string)Uuid::uuid4();
    }

    /**
     */
    public function testObject()
    {
        $user = new User($this->id, self::NAME, $this->getAddress());

        $this->assertEquals($this->id, $user->getId());
        $this->assertEquals(self::NAME, $user->getName());
        $this->assertEquals(self::CITY_VALUE, $user->getCity());
        $this->assertEquals(self::POST_VALUE, $user->getPost());
        $this->assertEquals(self::POSTCODE_VALUE, $user->getPostCode());
        $this->assertEquals(self::STREET_VALUE, $user->getStreet());
    }

    /**
     * @return Address|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getAddress()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|Address $address */
        $address = $this->getMockBuilder(Address::class)->disableOriginalConstructor()->getMock();
        $address->expects($this->any())->method('getStreet')->willReturn(self::STREET_VALUE);
        $address->expects($this->any())->method('getPost')->willReturn(self::POST_VALUE);
        $address->expects($this->any())->method('getPostCode')->willReturn(self::POSTCODE_VALUE);
        $address->expects($this->any())->method('getCity')->willReturn(self::CITY_VALUE);

        return $address;
    }
}
