<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

use Kitsunet\ImageManipulation\Blob\BlobMetadata;

/**
 * This is a generic ImageBlob implementation to be used as fallback and reference.
 */
class ImageBlob implements ImageBlobInterface
{
    use TemporaryFileFromStreamTrait;

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
     * @see \Kitsunet\ImageManipulation\ImageBlob\ImageBlobFactoryInterface
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
    public static function fromStream($stream, BlobMetadata $blobMetadata): self
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
     * @return BoxInterface
     */
    public function getSize(): BoxInterface
    {
        $fileExtension = MediaTypes::getFilenameExtensionFromMediaType($this->blobMetadata->getMediaType());
        $temporaryFilename = $this->getTemporaryFilename($fileExtension);
        $temporaryFile = fopen($temporaryFilename, 'w');
        stream_copy_to_stream($this->stream, $temporaryFile);
        fclose($temporaryFile);

        try {
            list($width, $height) = getimagesize($temporaryFilename);
            return new Box($width, $height);
        } catch (\Exception $exception) {
            return new EmptyBox();
        }
    }

    /**
     * @return resource
     */
    public function getStream()
    {
        return $this->stream;
    }
}
