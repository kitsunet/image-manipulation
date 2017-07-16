<?php
namespace Kitsunet\ImageManipulation\Blob;

interface BlobInterface
{
    /**
     * @return resource
     */
    public function getStream();

    /**
     * @return BlobMetadata
     */
    public function getMetadata();

    /**
     * API to create a new Blob regardless of how the actual __construct
     * of a specific implmentation might look like.
     *
     * @return BlobInterface
     */
    public static function fromStream($stream, BlobMetadata $blobMetadata);
}
