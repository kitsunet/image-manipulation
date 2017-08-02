<?php
namespace Kitsunet\ImageManipulation\Imagine;

use Imagine\Image\Box;
use Imagine\Image\Point;
use Kitsunet\ImageManipulation\ImageBlob\BoxInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\ImageManipulationInterface;

/**
 *
 */
class CropManipulation implements ImageManipulationInterface
{
    use ManipulationHelperTrait;

    /**
     * @var int
     */
    protected $x;

    /**
     * @var int
     */
    protected $y;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * CropManipulation constructor.
     *
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     */
    public function __construct(int $x, int $y, int $width, int $height)
    {
        $this->x = $x;
        $this->y = $y;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @param array $options
     * @return static
     */
    public static function fromOptions(array $options)
    {
        return new static((int)$options['x'], (int)$options['y'], (int)$options['width'], (int)$options['height']);
    }

    /**
     * @param \Kitsunet\ImageManipulation\ImageBlob\Point $point
     * @param BoxInterface $box
     * @return static
     */
    public static function fromPointAndBox(\Kitsunet\ImageManipulation\ImageBlob\Point $point, BoxInterface $box)
    {
        return new static($point->getX(), $point->getY(), $box->getWidth(), $box->getHeight());
    }

    /**
     * @param ImageBlobInterface $image
     * @return ImageBlobInterface|ImagineImageBlob
     */
    public function applyTo(ImageBlobInterface $image): ImageBlobInterface
    {
        $image = $this->upgradeToImagineBlob($image);

        $imagine = $image->getImagineImage();
        $imagine = $imagine->crop(new Point($this->x, $this->y), new Box($this->width, $this->height));
        return ImagineImageBlob::fromImagineImage($imagine, clone $image->getMetadata());
    }
}
