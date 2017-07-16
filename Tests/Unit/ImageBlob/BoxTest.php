<?php
namespace Kitsunet\ImageManipulation\Tests\Unit\ImageBlob;

use Kitsunet\ImageManipulation\ImageBlob\Box;
use Neos\Flow\Tests\UnitTestCase;

/**
 *
 */
class BoxTest extends UnitTestCase
{
    /**
     * @test
     */
    public function boxCanBeCreatedWithWidthAndHeight()
    {
        $box = new Box(5, 5);
        $this->assertInstanceOf(Box::class, $box);
    }

    /**
     * @test
     * @expectedException \TypeError
     */
    public function boxCannotBeCreatedWithJustOneParameter()
    {
        $box = new Box(5);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function widthCannotBeZero()
    {
        $box = new Box(0, 5);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function heightCannotBeZero()
    {
        $box = new Box(5, 0);
    }

    /**
     * @test
     */
    public function widenScalesProportionallyToGivenWidth()
    {
        $box = new Box(10, 10);
        $newBox = $box->widen(100);
        $this->assertNotEquals($newBox, $box);
        $this->assertEquals(100, $newBox->getHeight());
    }

    /**
     * @test
     */
    public function heightenScalesProportionallyToGivenWidth()
    {
        $box = new Box(10, 10);
        $newBox = $box->heighten(100);
        $this->assertNotEquals($newBox, $box);
        $this->assertEquals(100, $newBox->getWidth());
    }
}
