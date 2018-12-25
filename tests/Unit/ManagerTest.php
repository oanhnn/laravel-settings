<?php

namespace Laravel\Settings\Tests\Unit;

use Laravel\Settings\Drivers\Cache;
use Laravel\Settings\Drivers\Database;
use Laravel\Settings\Drivers\Json;
use Laravel\Settings\Drivers\Memory;
use Laravel\Settings\Manager;
use Laravel\Settings\Tests\NonPublicAccessibleTrait;
use Laravel\Settings\Tests\TestCase;

/**
 * Class ManagerTest
 *
 * @package     Laravel\Settings\Tests\Unit
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT license
 */
class ManagerTest extends TestCase
{
    use NonPublicAccessibleTrait;

    /**
     * Test getDefaultDriver method
     */
    public function testGetDefaultDriver()
    {
        config()->set('settings.driver', 'database');
        $manager = new Manager($this->app);

        $this->assertEquals('database', $manager->getDefaultDriver());
    }

    /**
     * @throws \ReflectionException
     */
    public function testCreateJsonDriver()
    {
        $manager = new Manager($this->app);

        $this->assertInstanceOf(Json::class, $this->invokeNonPublicMethod($manager, 'createJsonDriver'));
    }

    /**
     * @throws \ReflectionException
     */
    public function testCreateDatabaseDriver()
    {
        $manager = new Manager($this->app);

        $this->assertInstanceOf(Database::class, $this->invokeNonPublicMethod($manager, 'createDatabaseDriver'));
    }

    /**
     * @throws \ReflectionException
     */
    public function testCreateCacheDriver()
    {
        $manager = new Manager($this->app);

        $this->assertTrue(true);
        // TODO
        // $this->assertInstanceOf(Cache::class, $this->invokeNonPublicMethod($manager, 'createCacheDriver'));
    }

    /**
     * @throws \ReflectionException
     */
    public function testCreateMemoryDriver()
    {
        $manager = new Manager($this->app);

        $this->assertInstanceOf(Memory::class, $this->invokeNonPublicMethod($manager, 'createMemoryDriver'));
    }

    /**
     * @throws \ReflectionException
     */
    public function testCreateArrayDriver()
    {
        $manager = new Manager($this->app);

        $this->assertInstanceOf(Memory::class, $this->invokeNonPublicMethod($manager, 'createArrayDriver'));
    }
//
//    public function testCanGetDriver()
//    {}
//
//    public function testCanCustomDriver()
//    {}
}
