<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

use Kitsunet\ImageManipulation\Blob\BlobMetadata;

/**
 * This placeholder implementation should never be used and is most probably removed later on.
 */
class ImageBlob implements ImageBlobInterface
{
    /**
     * @var resource
     */
    protected $stream;

    /**
     * @var BlobMetadata
     */
    protected $blobMetadata;

    /**
     * ImageBlob constructor.
     *
     * @param $stream
     * @param BlobMetadata $blobMetadata
     */
    public function __construct($stream, BlobMetadata $blobMetadata)
    {
        $this->stream = $stream;
        $this->blobMetadata = $blobMetadata;
    }

    /**
     * @param $stream
     * @param BlobMetadata $blobMetadata
     * @return static
     */
    public static function fromStream($stream, BlobMetadata $blobMetadata)
    {
        return new static($stream, $blobMetadata);
    }

    /**
     * @return BlobMetadata
     */
    public function getMetadata(): BlobMetadata
    {
        return $this->blobMetadata;
    }

    /**
     * @return EmptyBox
     */
    public function getSize()
    {
        return new EmptyBox();
    }

    /**
     * @return resource
     */
    public function getStream()
    {
        return $this->stream;
    }
}
