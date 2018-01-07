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
    const NEW_NAME = 'John Doe the second';

    const POSTCODE_VALUE = '32-436';
    const STREET_VALUE = 'street';
    const POST_VALUE = 'post';
    const CITY_VALUE = 'city';

    const POSTCODE2_VALUE = '12-436';
    const STREET2_VALUE = 'street2';
    const POST2_VALUE = 'post2';
    const CITY2_VALUE = 'city2';

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

        $user->changeName(self::NEW_NAME);
        $this->assertEquals(self::NEW_NAME, $user->getName());

        $user->changeAddress($this->getSecondAddress());

        $this->assertEquals(self::CITY2_VALUE, $user->getCity());
        $this->assertEquals(self::POST2_VALUE, $user->getPost());
        $this->assertEquals(self::POSTCODE2_VALUE, $user->getPostCode());
        $this->assertEquals(self::STREET2_VALUE, $user->getStreet());
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

    /**
     * @return Address|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getSecondAddress()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|Address $address */
        $address = $this->getMockBuilder(Address::class)->disableOriginalConstructor()->getMock();
        $address->expects($this->any())->method('getStreet')->willReturn(self::STREET2_VALUE);
        $address->expects($this->any())->method('getPost')->willReturn(self::POST2_VALUE);
        $address->expects($this->any())->method('getPostCode')->willReturn(self::POSTCODE2_VALUE);
        $address->expects($this->any())->method('getCity')->willReturn(self::CITY2_VALUE);

        return $address;
    }
}
