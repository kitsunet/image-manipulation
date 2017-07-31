<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

use Kitsunet\ImageManipulation\Blob\BlobMetadata;

/**
 * Contract for a factory that can create image blobs
 */
interface ImageBlobFactoryInterface
{
    /**
     * Create Imageblob from the given stream and metadata.
     *
     * @param resource $stream
     * @param BlobMetadata $blobMetadata
     * @return ImageBlobInterface
     */
    public function create($stream, BlobMetadata $blobMetadata): ImageBlobInterface;
}
