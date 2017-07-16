<?php
namespace Kitsunet\ImageManipulation\Tests\Unit\ImageBlob;

use Kitsunet\ImageManipulation\Blob\BlobMetadata;
use Kitsunet\ImageManipulation\ImageBlob\EmptyBox;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlob;
use Neos\Flow\Tests\UnitTestCase;

/**
 *
 */
class ImageBlobTest extends UnitTestCase
{
    /**
     * @test
     */
    public function imageBlobCanBeCreatedFromStream()
    {
        $metadataMock = $this->getMockBuilder(BlobMetadata::class)->disableOriginalConstructor()->getMock();
        $testStream = fopen('php://temp', 'r');
        $imageBlob = ImageBlob::fromStream($testStream, $metadataMock);
        $this->assertInstanceOf(ImageBlob::class, $imageBlob);
    }

    /**
     * @test
     */
    public function canRetrieveStreamFromImageBlob()
    {
        $metadataMock = $this->getMockBuilder(BlobMetadata::class)->disableOriginalConstructor()->getMock();
        $testStream = fopen('php://temp', 'r');
        $imageBlob = ImageBlob::fromStream($testStream, $metadataMock);
        $this->assertEquals($testStream, $imageBlob->getStream());
    }

    /**
     * @test
     */
    public function imageBlobHasNoWayToDetermineSize()
    {
        $metadataMock = $this->getMockBuilder(BlobMetadata::class)->disableOriginalConstructor()->getMock();
        $testStream = fopen('php://temp', 'r');
        $imageBlob = ImageBlob::fromStream($testStream, $metadataMock);
        $this->assertInstanceOf(EmptyBox::class, $imageBlob->getSize());
    }
}
