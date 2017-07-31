<?php
namespace Kitsunet\ImageManipulation\Blob;

/**
 * Contract for a generic blob of data and metadata.
 *
 */
interface BlobInterface
{
    /**
     * Get a stream resource for this blob
     *
     * @return resource
     */
    public function getStream();

    /**
     * Get the metadata for this blob.
     *
     * @return BlobMetadata
     */
    public function getMetadata(): BlobMetadata;

    /**
     * API to create a new Blob regardless of how the actual __construct
     * of a specific implmentation might look like.
     *
     * @param $stream
     * @param BlobMetadata $blobMetadata
     * @return BlobInterface
     */
    public static function fromStream($stream, BlobMetadata $blobMetadata);
}
