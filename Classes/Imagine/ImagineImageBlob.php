<?php
namespace Kitsunet\ImageManipulation\Imagine;

use Imagine\Filter\Basic\WebOptimization;
use Imagine\Image\ImagineInterface;
use Imagine\Image\ImageInterface;
use Kitsunet\ImageManipulation\Blob\BlobMetadata;
use Kitsunet\ImageManipulation\ImageBlob\Box;
use Kitsunet\ImageManipulation\ImageBlob\BoxInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Neos\Imagine\ImagineFactory;

/**
 *
 */
class ImagineImageBlob implements ImageBlobInterface
{
    /**
     * @var resource
     */
    protected $stream;

    /**
     * @var ImagineInterface
     */
    protected $imagineImage;

    /**
     * @var BlobMetadata
     */
    protected $blobMetadata;

    /**
     * ImagineImageBlob constructor
     *
     * @param ImageInterface $imagineImage
     * @param BlobMetadata $blobMetadata
     */
    public function __construct(ImageInterface $imagineImage, BlobMetadata $blobMetadata)
    {
        $this->imagineImage = $imagineImage;
        $this->blobMetadata = $blobMetadata;
    }

    /**
     * @param resource $stream
     * @param BlobMetadata $blobMetadata
     * @return static
     */
    public static function fromStream($stream, BlobMetadata $blobMetadata): ImagineImageBlob
    {
        $factory = new ImagineFactory();
        $imagine = $factory->create();
        return new static($imagine->read($stream), $blobMetadata);
    }

    /**
     * @param ImageInterface $imagineImage
     * @param BlobMetadata $blobMetadata
     * @return static
     */
    public static function fromImagineImage(ImageInterface $imagineImage, BlobMetadata $blobMetadata): ImagineImageBlob
    {
        return new static($imagineImage, $blobMetadata);
    }

    /**
     * @return BlobMetadata
     */
    public function getMetadata(): BlobMetadata
    {
        return $this->blobMetadata;
    }

    /**
     * @return ImageInterface
     */
    public function getImagineImage(): ImageInterface
    {
        return $this->imagineImage;
    }

    /**
     * @return resource
     */
    public function getStream()
    {
        return $this->getStreamInternal();
    }

    /**
     * @return BoxInterface|Box
     */
    public function getSize(): BoxInterface
    {
        $imagineBox = $this->imagineImage->getSize();
        return new Box($imagineBox->getWidth(), $imagineBox->getHeight());
    }

    /**
     * @return resource
     */
    protected function getStreamInternal()
    {
        $file = $this->getTemporaryFilename();
        if (!file_exists($file)) {
            $webOptimization = new WebOptimization($file, ($this->blobMetadata->getProperty('options') ?? []));
            $webOptimization->apply($this->imagineImage);
        }
        $temp = fopen($file, 'r');
        return $temp;
    }

    /**
     * Generate a temporary filename unique to this instance
     *
     * @return string
     */
    protected function getTemporaryFilename(): string
    {
        $extension = '.' . $this->blobMetadata->getProperty('fileExtension') ?? 'png';
        return FLOW_PATH_TEMPORARY . 'imagineblob_temporary_' . getmypid() . '_' . spl_object_hash($this->imagineImage) . $extension;
    }

    /**
     * removes any leftover temporary files
     */
    public function __destruct()
    {
        $filename = $this->getTemporaryFilename();
        @unlink($filename);
    }
}
