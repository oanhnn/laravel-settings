<?php

namespace Laravel\Settings\Tests\Unit;

use Laravel\Settings\Drivers\Driver;
use Laravel\Settings\Facades\Settings;
use Laravel\Settings\Tests\TestCase;

/**
 * Class HelperTest
 *
 * @package     Laravel\Settings\Tests\Unit
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT license
 */
class HelperTest extends TestCase
{
    /**
     * Test call without parameters, this will return an instance of store driver
     */
    public function testCallWithoutParameters()
    {
        $this->assertInstanceOf(Driver::class, settings());
    }

    /**
     * Test call with single string parameter, this will get a key from store
     */
    public function testCallWithSingleStringParameter()
    {
        Settings::shouldReceive('get')->with('foo', null)->once();

        settings('foo');
    }

    /**
     * Test call with two string parameter, this will get a key from store with default
     */
    public function testCallWithTwoStringParameters()
    {
        Settings::shouldReceive('get')->with('foo', 'bar')->once();

        settings('foo', 'bar');
    }

    /**
     * Test call with single array parameter, this will set into store
     */
    public function testCallWithSingleArrayParameter()
    {
        Settings::shouldReceive('set')->with(['foo' => 'bar'])->once();

        settings(['foo' => 'bar']);
    }
}
