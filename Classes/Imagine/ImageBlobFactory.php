<?php
namespace Kitsunet\ImageManipulation\Imagine;

use Kitsunet\ImageManipulation\Blob\BlobMetadata;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobFactoryInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Neos\Imagine\ImagineFactory;

/**
 *
 */
class ImageBlobFactory implements ImageBlobFactoryInterface
{
    /**
     * @param resource $stream
     * @param BlobMetadata $blobMetadata
     * @return ImagineImageBlob
     */
    public function create($stream, BlobMetadata $blobMetadata): ImageBlobInterface
    {
        $factory = new ImagineFactory();
        $imagine = $factory->create();

        return new ImagineImageBlob($imagine->read($stream), $blobMetadata);
    }
}
