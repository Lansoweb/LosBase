<?php
namespace LosBaseTest\Entity;

use LosBaseTest\TestCase;

/**
 * BrPriceType test case.
 */
class AbstractEntityTest extends TestCase
{
    private $entity;

    protected function setUp()
    {
        parent::setUp();

        $this->entity = $this->getMockForAbstractClass('LosBase\Entity\AbstractEntity');
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

    public function testHasId()
    {
        $this->assertObjectHasAttribute('id', $this->entity);
        $this->assertTrue(method_exists($this->entity, 'getId'));
        $this->assertTrue(method_exists($this->entity, 'setId'));
    }

    public function testUseId()
    {
        $ids = [2, '2'];
        foreach ($ids as $id) {
            $this->assertSame($this->entity, $this->entity->setId($id));
            $this->assertSame(2, $this->entity->getId());
        }
    }

    public function testHasCreated()
    {
        $this->assertObjectHasAttribute('created', $this->entity);
        $this->assertTrue(method_exists($this->entity, 'getCreated'));
        $this->assertTrue(method_exists($this->entity, 'setCreated'));
    }

    public function testUseCreated()
    {
        $dt = new \DateTime('now');
        $this->assertInstanceOf('DateTime', $this->entity->getCreated());
        $this->assertSame($this->entity, $this->entity->setCreated($dt));
        $this->assertSame($dt, $this->entity->getCreated());
    }

    public function testHasUpdated()
    {
        $this->assertObjectHasAttribute('updated', $this->entity);
        $this->assertTrue(method_exists($this->entity, 'getUpdated'));
        $this->assertTrue(method_exists($this->entity, 'setUpdated'));
    }

    public function testUseUpdated()
    {
        $dt = new \DateTime('now');
        $this->assertInstanceOf('DateTime', $this->entity->getUpdated());
        $this->assertSame($this->entity, $this->entity->setUpdated($dt));
        $this->assertSame($dt, $this->entity->getUpdated());
    }
}
