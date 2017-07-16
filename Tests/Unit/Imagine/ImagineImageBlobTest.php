<?php
namespace Kitsunet\ImageManipulation\Tests\Unit\Imagine;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Kitsunet\ImageManipulation\Blob\BlobMetadata;
use Kitsunet\ImageManipulation\ImageBlob\BoxInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\Imagine\ImagineImageBlob;
use Neos\Flow\Tests\UnitTestCase;

/**
 *
 */
class ImagineImageBlobTest extends UnitTestCase
{
    /**
     * @test
     */
    public function imagineImageBlobCanBeCreatedFromImagineImage()
    {
        $metadataMock = $this->getMockBuilder(BlobMetadata::class)->disableOriginalConstructor()->getMock();
        $imagineImage = $this->getMockBuilder(ImageInterface::class)->disableOriginalConstructor()->getMock();
        $imagineBlob = ImagineImageBlob::fromImagineImage($imagineImage, $metadataMock);
        $this->assertInstanceOf(ImagineImageBlob::class, $imagineBlob);
        $this->assertInstanceOf(ImageBlobInterface::class, $imagineBlob);
    }

    /**
     * @test
     */
    public function imagineBlobCanDetermineSize()
    {
        $metadataMock = $this->getMockBuilder(BlobMetadata::class)->disableOriginalConstructor()->getMock();
        $imagineImage = $this->getMockBuilder(ImageInterface::class)->disableOriginalConstructor()->getMock();
        $imagineImage->expects(self::any())->method('getSize')->willReturn(new Box(5, 10));
        $imagineBlob = ImagineImageBlob::fromImagineImage($imagineImage, $metadataMock);
        $size = $imagineBlob->getSize();
        $this->assertInstanceOf(BoxInterface::class, $size);
        $this->assertEquals(5, $size->getWidth());
        $this->assertEquals(10, $size->getHeight());
    }
}
