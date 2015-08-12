<?php

namespace LosBaseTest\DBAL\Types;

use LosBase\DBAL\Types\UtcDateTimeType;
use LosBaseTest\TestCase;
use Doctrine\DBAL\Types\Type;
use LosBaseTest\DBAL\Mocks\MockPlatform;

/**
 * UtcDateTimeType test case.
 */
class UtcDateTimeTypeTest extends TestCase
{
    /**
     * @var UtcDateTimeType
     */
    private $UtcDateTimeType;
    private $platform;
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->platform = new MockPlatform();
        if (!Type::hasType('utcdatetime')) {
            Type::addType('utcdatetime', 'LosBase\DBAL\Types\UtcDateTimeType');
        }
        $this->UtcDateTimeType = Type::getType('utcdatetime');
        date_default_timezone_set('America/New_York');
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated UtcDateTimeTypeTest::tearDown()
        $this->UtcDateTimeType = null;

        parent::tearDown();
    }

    /**
     * Tests UtcDateTimeType->convertToDatabaseValue().
     */
    public function testConvertToDatabaseValue()
    {
        $dt = \DateTime::createFromFormat('d/m/Y H:i:s', '05/02/2015 09:10:11');
        $this->assertSame('2015-02-05 14:10:11', $this->UtcDateTimeType->convertToDatabaseValue($dt, $this->platform));
    }

    /**
     * Tests UtcDateTimeType->convertToPHPValue().
     */
    public function testConvertToPHPValue()
    {
        $dt = \DateTime::createFromFormat('d/m/Y H:i:s', '05/02/2015 09:10:11', new \DateTimeZone('UTC'));
        $this->assertEquals($dt, $this->UtcDateTimeType->convertToPHPValue('2015-02-05 09:10:11', $this->platform));
    }
}
