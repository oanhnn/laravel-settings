<?php

namespace Laravel\Settings\Tests\Unit;

use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;
use Laravel\Settings\Drivers\Driver;
use Laravel\Settings\Manager;
use Laravel\Settings\Tests\TestCase;

/**
 * Class ServiceProviderTest
 *
 * @package     Laravel\Settings\Tests\Unit
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT license
 */
class ServiceProviderTest extends TestCase
{
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Set up before test
     */
    protected function setUp()
    {
        Carbon::setTestNow(Carbon::create(2018, 12, 24, 0, 22, 0));
        parent::setUp();

        $this->files = new Filesystem();
    }

    /**
     * Clear up after test
     */
    protected function tearDown()
    {
        $this->files->delete([
            $this->app->configPath('settings.php'),
            $this->app->databasePath('migrations/2018_12_24_002200_create_settings_table.php'),
        ]);

        parent::tearDown();
        Carbon::setTestNow();
    }

    /**
     * Test file settings.php is existed in config directory after run
     *
     * php artisan vendor:publish --provider="Laravel\\Settings\\ServiceProvider" --tag=config
     */
    public function testPublishVendorConfig()
    {
        $sourceFile = dirname(dirname(__DIR__)) . '/config/settings.php';
        $targetFile = config_path('settings.php');

        $this->assertFileNotExists($targetFile);

        $this->artisan('vendor:publish', [
            '--provider' => 'Laravel\\Settings\\ServiceProvider',
            '--tag' => 'config',
        ]);

        $this->assertFileExists($targetFile);
        $this->assertEquals(file_get_contents($sourceFile), file_get_contents($targetFile));
    }

    /**
     * Test file xxxx_xx_xx_xxxxxx_create_settings_table.php is existed in config directory after run
     *
     * php artisan vendor:publish --provider="Laravel\\Settings\\ServiceProvider" --tag=migration
     */
    public function testPublishVendorMigration()
    {
        $sourceFile = dirname(dirname(__DIR__)) . '/database/migrations/create_settings_table.php';
        $targetFile = database_path('migrations/2018_12_24_002200_create_settings_table.php');

        $this->assertFileNotExists($targetFile);

        $this->artisan('vendor:publish', [
            '--provider' => 'Laravel\\Settings\\ServiceProvider',
            '--tag' => 'migration',
        ]);

        $this->assertFileExists($targetFile);
        $this->assertEquals(file_get_contents($sourceFile), file_get_contents($targetFile));
    }

    /**
     * Test settings override some config values
     */
    public function testOverrideConfigValues()
    {
        // TODO
        $this->assertTrue(true);
    }

    /**
     * Test config values are merged
     */
    public function testDefaultConfigValues()
    {
        // default driver
        $this->assertEquals('database', config('settings.driver'));
        // json store config
        $this->assertEquals(storage_path('app/settings.json'), config('settings.json.path'));
        // cache store config
        $this->assertEquals(null, config('settings.cache.store'));
        $this->assertEquals('app_settings', config('settings.cache.key'));
        $this->assertEquals(null, config('settings.cache.expired'));
        $this->assertEquals('database', config('settings.cache.failback'));
        // database store config
        $this->assertEquals(null, config('settings.database.connection'));
        $this->assertEquals('settings', config('settings.database.table'));
        $this->assertEquals('key', config('settings.database.key'));
        $this->assertEquals('value', config('settings.database.value'));
        // settings override
        $this->assertEquals([], config('settings.override'));
    }

    /**
     * Test manager is bound in application container
     */
    public function testBoundManager()
    {
        $this->assertTrue($this->app->bound('settings.manager'));
        $this->assertInstanceOf(Manager::class, $this->app->get('settings.manager'));

        $this->assertTrue($this->app->bound('settings'));
        $this->assertInstanceOf(Driver::class, $this->app->get('settings'));
    }
}
