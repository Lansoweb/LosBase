<?php
namespace LosBaseTest\Module;

use LosBaseTest\TestCase;

/**
 * AbstractModule test case.
 */
class AbstractModuleTest extends TestCase
{
    /**
     *
     * @var AbstractModule
     */
    private $AbstractModule;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        // TODO Auto-generated AbstractModuleTest::setUp()

        $this->AbstractModule = $this->getMockForAbstractClass('LosBase\Module\AbstractModule');
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated AbstractModuleTest::tearDown()
        $this->AbstractModule = null;

        parent::tearDown();
    }

    /**
     * Tests AbstractModule->getAutoloaderConfig()
     */
    public function testGetAutoloaderConfig()
    {
        $config = $this->AbstractModule->getAutoloaderConfig();
        $this->assertArrayHasKey('Zend\Loader\ClassMapAutoloader', $config);
        $this->assertArrayHasKey('Zend\Loader\StandardAutoloader', $config);
    }
}
