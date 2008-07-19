<?php


require_once 'trunk/incubator/library/Zym/Dto.php';


/**
 * Zym_Dto test case.
 */
class Zym_DtoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zym_Dto
     */
    private $Zym_Dto;


    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->Zym_Dto = new Zym_Dto(array('foo' => 'foo',
                                           'bar' => 'bar',
                                           'bat' => 'bat'));
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->Zym_Dto = null;

        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct()
    {
    }

    /**
     * Tests Zym_Dto->current()
     */
    public function testCurrent()
    {
        $current = $this->Zym_Dto->current();
        $this->assertEquals('foo', $current);
    }

    /**
     * Tests Zym_Dto->getValue()
     */
    public function testGetValue()
    {
        $value = $this->Zym_Dto->getValue('foo');
        $this->assertEquals('foo', $value);
    }

    /**
     * Tests Zym_Dto->hasValue()
     */
    public function testHasValue()
    {
        $hasValue = $this->Zym_Dto->hasValue('foo');
        $this->assertTrue($hasValue);
    }

    /**
     * Tests Zym_Dto->key()
     */
    public function testKey()
    {
        $key = $this->Zym_Dto->key(/* parameters */);
        $this->assertEquals('foo', $key);
    }

    /**
     * Tests Zym_Dto->next()
     */
    public function testNext()
    {
        $this->Zym_Dto->next();
        $this->assertEquals('bar', $this->Zym_Dto->current());
        $this->Zym_Dto->rewind();
    }

    /**
     * Tests Zym_Dto->offsetExists()
     */
    public function testOffsetExists()
    {
        $exists = $this->Zym_Dto->offsetExists('foo');
        $this->assertTrue($exists);
    }

    /**
     * Tests Zym_Dto->offsetGet()
     */
    public function testOffsetGet()
    {
        $value = $this->Zym_Dto->offsetGet('foo');
        $this->assertEquals('foo', $value);
    }

    /**
     * Tests Zym_Dto->offsetSet()
     */
    public function testOffsetSet()
    {
        $this->Zym_Dto->offsetSet('baz', 'baz');
        $has = $this->Zym_Dto->hasValue('baz');
        $this->assertTrue($has);
        $this->Zym_Dto->removeValue('baz');
    }

    /**
     * Tests Zym_Dto->offsetUnset()
     */
    public function testOffsetUnset()
    {
        $this->Zym_Dto->offsetSet('baz', 'baz');
        $this->assertTrue($this->Zym_Dto->hasValue('baz'));
        $this->Zym_Dto->offsetUnset('baz');
        $this->assertFalse($this->Zym_Dto->hasValue('baz'));
    }

    /**
     * Tests Zym_Dto->removeValue()
     */
    public function testRemoveValue()
    {
        $this->Zym_Dto->setValue('baz', 'baz');
        $this->assertTrue($this->Zym_Dto->hasValue('baz'));
        $this->Zym_Dto->removeValue('baz');
        $this->assertFalse($this->Zym_Dto->hasValue('baz'));
    }

    /**
     * Tests Zym_Dto->rewind()
     */
    public function testRewind()
    {
        $this->Zym_Dto->next();
        $this->Zym_Dto->next();
        $this->Zym_Dto->rewind();
        $this->assertEquals('foo', $this->Zym_Dto->current());
    }

    /**
     * Tests Zym_Dto->serialize()
     */
    public function testSerialize()
    {
        $serialized = $this->Zym_Dto->serialize();
        $this->assertEquals(serialize($this->Zym_Dto->toArray()), $serialized);
    }

    /**
     * Tests Zym_Dto->setFromArray()
     */
    public function testSetFromArray()
    {
        $newData = array('baz' => 'baz');

        $this->Zym_Dto->setFromArray($newData);

        $array = $this->Zym_Dto->toArray();

        $this->assertEquals($newData, $array);

        $this->Zym_Dto->setFromArray(array('foo' => 'foo',
                                           'bar' => 'bar',
                                           'bat' => 'bat'));
    }

    /**
     * Tests Zym_Dto->setValue()
     */
    public function testSetValue()
    {
        $this->Zym_Dto->setValue('baz', 'baz');
        $this->assertTrue($this->Zym_Dto->hasValue('baz'));
        $this->Zym_Dto->removeValue('baz');
    }

    /**
     * Tests Zym_Dto->toArray()
     */
    public function testToArray()
    {
        $array = $this->Zym_Dto->toArray();
        $this->assertEquals(array('foo' => 'foo',
                                  'bar' => 'bar',
                                  'bat' => 'bat'), $array);
    }

    /**
     * Tests Zym_Dto->unserialize()
     */
    public function testUnserialize()
    {
        // TODO Auto-generated Zym_DtoTest->testUnserialize()
        $this->markTestIncomplete("unserialize test not implemented");

        $this->Zym_Dto->unserialize(/* parameters */);

    }

    /**
     * Tests Zym_Dto->valid()
     */
    public function testValid()
    {
        $this->Zym_Dto->rewind();
        $this->assertTrue($this->Zym_Dto->valid());
    }

    /**
     * Tests Zym_Dto->__construct()
     */
    public function test__construct()
    {
        // TODO Auto-generated Zym_DtoTest->test__construct()
        $this->markTestIncomplete("__construct test not implemented");

        $this->Zym_Dto->__construct(/* parameters */);

    }

    /**
     * Tests Zym_Dto->__get()
     */
    public function test__get()
    {
        // TODO Auto-generated Zym_DtoTest->test__get()
        $this->markTestIncomplete("__get test not implemented");

        $this->Zym_Dto->__get(/* parameters */);

    }

    /**
     * Tests Zym_Dto->__set()
     */
    public function test__set()
    {
        // TODO Auto-generated Zym_DtoTest->test__set()
        $this->markTestIncomplete("__set test not implemented");

        $this->Zym_Dto->__set(/* parameters */);

    }

    /**
     * Tests Zym_Dto->__toString()
     */
    public function test__toString()
    {
        // TODO Auto-generated Zym_DtoTest->test__toString()
        $this->markTestIncomplete("__toString test not implemented");

        $this->Zym_Dto->__toString(/* parameters */);

    }

    /**
     * Tests Zym_Dto->__unset()
     */
    public function test__unset()
    {
        // TODO Auto-generated Zym_DtoTest->test__unset()
        $this->markTestIncomplete("__unset test not implemented");

        $this->Zym_Dto->__unset(/* parameters */);

    }
}