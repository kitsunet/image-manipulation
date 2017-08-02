<?php
namespace Kitsunet\ImageManipulation\Imagine;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Kitsunet\ImageManipulation\ImageBlob\BoxInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
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
     * @var string
     */
    protected $filter;

    /**
     * ResizeManipulation constructor.
     *
     * @param int $width
     * @param int $height
     * @param string $filter
     */
    protected function __construct(int $width = 0, int $height = 0, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        $this->width = $width;
        $this->height = $height;
        $this->filter = $filter;
    }

    /**
     * @param int $width
     * @param string $filter
     * @return ResizeManipulation
     */
    public static function toWidth(int $width, $filter = ImageInterface::FILTER_UNDEFINED): self
    {
        return new static($width, null, $filter);
    }

    /**
     * @param int $height
     * @param string $filter
     * @return ResizeManipulation
     */
    public static function toHeight(int $height, $filter = ImageInterface::FILTER_UNDEFINED): self
    {
        return new static(null, $height, $filter);
    }

    /**
     * @param int $width
     * @param int $height
     * @param string $filter
     * @return ResizeManipulation
     */
    public static function toDimensions(int $width, int $height, $filter = ImageInterface::FILTER_UNDEFINED): self
    {
        return new static($width, $height, $filter);
    }

    /**
     * @param BoxInterface $box
     * @param string $filter
     * @return static
     */
    public static function toBox(BoxInterface $box, $filter = ImageInterface::FILTER_UNDEFINED): self
    {
        return new static($box->getWidth(), $box->getHeight(), $filter);
    }

    /**
     * @param array $options
     * @return static
     */
    public static function fromOptions(array $options): self
    {
        return new static((int)$options['width'], (int)$options['height'], ($options['filter'] ?? null));
    }

    /**
     * @param ImageBlobInterface $image
     * @return ImageBlobInterface|ImagineImageBlob
     */
    public function applyTo(ImageBlobInterface $image): ImageBlobInterface
    {
        $image = $this->upgradeToImagineBlob($image);

        $imagine = $image->getImagineImage();
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


        $imagine = $imagine->resize($size, $this->filter);
        return ImagineImageBlob::fromImagineImage($imagine, clone $image->getMetadata());
    }
}
