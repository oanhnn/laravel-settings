<?php

namespace Laravel\Settings\Tests\Unit\Drivers;

use Laravel\Settings\Drivers\Driver;
use Laravel\Settings\Tests\NonPublicAccessibleTrait;
use Laravel\Settings\Tests\TestCase;

/**
 * Class DriverTest
 *
 * @package     Laravel\Settings\Tests\Unit\Drivers
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT license
 */
class DriverTest extends TestCase
{
    use NonPublicAccessibleTrait;

    /**
     * Test load data only data hasn't set yet
     *
     * @throws \ReflectionException
     */
    public function testLoadWhenDataNotSetYet()
    {
        $store = $this->getMockForAbstractClass(Driver::class);

        $store->expects($this->once())
            ->method('read')
            ->willReturn(['foo' => 'bar']);

        $store->load();

        // check property after load
        $this->assertTrue($this->getNonPublicProperty($store, 'loaded'));
        $this->assertFalse($this->getNonPublicProperty($store, 'unsaved'));
        $this->assertEquals(['foo' => 'bar'], $this->getNonPublicProperty($store, 'data'));

        // check re-call `load` method but `read` method don't run
        $store->load();
    }

    /**
     * Test re-load data when using force load
     *
     * @throws \ReflectionException
     */
    public function testForceLoad()
    {
        $store = $this->getMockForAbstractClass(Driver::class);

        $store->expects($this->exactly(2))
            ->method('read')
            ->willReturn(['foo' => 'bar']);

        $store->load();
        $store->load(true);

        // check property after load
        $this->assertTrue($this->getNonPublicProperty($store, 'loaded'));
        $this->assertFalse($this->getNonPublicProperty($store, 'unsaved'));
        $this->assertEquals(['foo' => 'bar'], $this->getNonPublicProperty($store, 'data'));
    }

    /**
     * @param  array  $data
     * @param  string $key
     * @param  mixed  $expected
     * @throws \Exception
     * @dataProvider provideTestGetData
     */
    public function testGetReturnCorrectValue(array $data, $key, $expected)
    {
        $store = $this->mockStoreWithData($data);

        $this->assertEquals($expected, $store->get($key));
    }

    /**
     * @return array
     */
    public function provideTestGetData()
    {
        return [
            [[], 'foo', null],
            [['foo' => 'bar'], 'foo', 'bar'],
            [['foo' => 'bar'], 'bar', null],
            [['foo' => 'bar'], 'foo.bar', null],
            [['foo' => ['bar' => 'baz']], 'foo.bar', 'baz'],
            [['foo' => ['bar' => 'baz']], 'foo.baz', null],
            [['foo' => ['bar' => 'baz']], 'foo', ['bar' => 'baz']],
            [
                ['foo' => 'bar', 'bar' => 'baz'],
                ['foo', 'bar'],
                ['foo' => 'bar', 'bar' => 'baz'],
            ],
            [
                ['foo' => ['bar' => 'baz'], 'bar' => 'baz'],
                ['foo.bar', 'bar'],
                ['foo' => ['bar' => 'baz'], 'bar' => 'baz'],
            ],
            [
                ['foo' => ['bar' => 'baz'], 'bar' => 'baz'],
                ['foo.bar'],
                ['foo' => ['bar' => 'baz']],
            ],
            [
                ['foo' => ['bar' => 'baz'], 'bar' => 'baz'],
                ['foo.bar', 'baz'],
                ['foo' => ['bar' => 'baz'], 'baz' => null],
            ],
        ];
    }

    /**
     * @param array $data
     * @return Driver|\PHPUnit\Framework\MockObject\MockObject
     * @throws \Exception
     */
    protected function mockStoreWithData(array $data)
    {
        $store = $this->getMockForAbstractClass(Driver::class);

        $store->expects($this->once())
            ->method('read')
            ->willReturn($data);

        return $store;
    }
}
