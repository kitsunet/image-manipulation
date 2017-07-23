<?php
namespace Kitsunet\ImageManipulation\Imagine;

use Imagine\Image\Box;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\ManipulationDescriptionInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\ImageManipulationInterface;

/**
 *
 */
class ResizeManipulation implements ImageManipulationInterface
{
    use ManipulationHelperTrait;

    /**
     * @var int
     */
    protected $width = 0;

    /**
     * @var int
     */
    protected $height = 0;

    /**
     * ResizeManipulation constructor.
     *
     * @param int $width
     * @param int $height
     */
    protected function __construct(int $width = 0, int $height = 0)
    {
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @param int $width
     * @return static
     */
    public static function toWidth(int $width)
    {
        return new static($width);
    }

    /**
     * @param int $height
     * @return static
     */
    public static function toHeight(int $height)
    {
        return new static(null, $height);
    }

    /**
     * @param int $width
     * @param int $height
     * @return static
     */
    public static function toDimensions(int $width, int $height)
    {
        return new static($width, $height);
    }

    /**
     * @param ManipulationDescriptionInterface $description
     * @return static
     */
    public static function fromDescription(ManipulationDescriptionInterface $description)
    {
        $options = $description->getOptions();
        return new static($options['width'], $options['height']);
    }

    /**
     * @param ImageBlobInterface $image
     * @return ImageBlobInterface|ImagineImageBlob
     */
    public function applyTo(ImageBlobInterface $image): ImageBlobInterface
    {
        $imagine = $this->getImagineImage($image);
        $originalSize = $imagine->getSize();
        $size = clone $originalSize;

        if ($this->width > 0) {
            $size = $originalSize->widen($this->width);
        }

        if ($this->height > 0) {
            $size = $originalSize->heighten($this->height);
        }

        if ($this->width > 0 && $this->height > 0) {
            $size = new Box($this->width, $this->height);
        }


        $imagine = $imagine->resize($size);
        return ImagineImageBlob::fromImagineImage($imagine, $image->getMetadata());
    }
}
