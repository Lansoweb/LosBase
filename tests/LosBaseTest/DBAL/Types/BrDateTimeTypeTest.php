<?php
namespace LosBaseTest\DBAL\Types;

use LosBaseTest\TestCase;
use Doctrine\DBAL\Types\Type;
use LosBaseTest\DBAL\Mocks\MockPlatform;

/**
 * UtcDateTimeType test case.
 */
class BrDateTimeTypeTest extends TestCase
{
    /**
     *
     * @var UtcDateTimeType
     */
    private $BrDateTimeType;
    private $platform;
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->platform = new MockPlatform();
        if (!Type::hasType('brdatetime')) {
            Type::addType('brdatetime', 'LosBase\DBAL\Types\BrDateTimeType');
        }
        $this->BrDateTimeType = Type::getType('brdatetime');
        date_default_timezone_set('America/New_York');
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated UtcDateTimeTypeTest::tearDown()
        $this->BrDateTimeType = null;

        parent::tearDown();
    }

    /**
     * Tests UtcDateTimeType->convertToDatabaseValue()
     */
    public function testConvertToDatabaseValue()
    {
        $dt = \DateTime::createFromFormat('d/m/Y H:i:s', '05/02/2015 09:10:11', new \DateTimeZone('America/Sao_Paulo'));
        $this->assertSame('2015-02-05 11:10:11', $this->BrDateTimeType->convertToDatabaseValue($dt, $this->platform));
    }

    /**
     * Tests UtcDateTimeType->convertToPHPValue()
     */
    public function testConvertToPHPValue()
    {
        $dt = \DateTime::createFromFormat('d/m/Y H:i:s', '05/02/2015 09:10:11', new \DateTimeZone('America/Sao_Paulo'));
        $this->assertEquals($dt, $this->BrDateTimeType->convertToPHPValue('2015-02-05 11:10:11', $this->platform));
    }
}
