<?php
namespace LosBaseTest\DBAL\Types;

use LosBaseTest\TestCase;
use Doctrine\DBAL\Types\Type;
use LosBaseTest\DBAL\Mocks\MockPlatform;

/**
 * BrPriceType test case.
 */
class BrPriceTypeTest extends TestCase
{
    /**
     *
     * @var BrPriceType
     */
    private $BrPriceType;
    private $platform;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->platform = new MockPlatform();
        if (!Type::hasType('brprice')) {
            Type::addType('brprice', 'LosBase\DBAL\Types\BrPriceType');
        }
        $this->BrPriceType = Type::getType('brprice');
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated BrPriceTypeTest::tearDown()
        $this->BrPriceType = null;

        parent::tearDown();
    }

    /**
     * Tests BrPriceType->convertToDatabaseValue()
     */
    public function testConvertToDatabaseValue()
    {
        $this->assertSame(1234.56, $this->BrPriceType->convertToDatabaseValue('1.234,56', $this->platform));
    }

    /**
     * Tests BrPriceType->convertToPHPValue()
     */
    public function testConvertToPHPValue()
    {
        $this->assertSame('1.234,56', $this->BrPriceType->convertToPHPValue('1234.56', $this->platform));
    }

    /**
     * Tests BrPriceType->getSQLDeclaration()
     */
    public function testGetSQLDeclaration()
    {
        $this->assertSame('NUMERIC(9, 2)', $this->BrPriceType->getSQLDeclaration([], $this->platform));
        $this->assertSame('NUMERIC(7, 2)', $this->BrPriceType->getSQLDeclaration(['precision' => 7, 'decimal' => 3], $this->platform));
    }
}
