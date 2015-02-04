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
     *
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
     * Constructs the test case.
     */
    public function __construct()
    {
        // TODO Auto-generated constructor
    }

    /**
     * Tests UtcDateTimeType->convertToDatabaseValue()
     */
    public function testConvertToDatabaseValue()
    {
        // TODO Auto-generated UtcDateTimeTypeTest->testConvertToDatabaseValue()
        $this->markTestIncomplete("convertToDatabaseValue test not implemented");

        $this->UtcDateTimeType->convertToDatabaseValue(/* parameters */);
    }

    /**
     * Tests UtcDateTimeType->convertToPHPValue()
     */
    public function testConvertToPHPValue()
    {
        // TODO Auto-generated UtcDateTimeTypeTest->testConvertToPHPValue()
        $this->markTestIncomplete("convertToPHPValue test not implemented");

        $this->UtcDateTimeType->convertToPHPValue(/* parameters */);
    }
}

