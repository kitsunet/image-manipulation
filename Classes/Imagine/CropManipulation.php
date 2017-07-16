<?php
namespace Kitsunet\ImageManipulation\Imagine;

use Imagine\Image\Box;
use Imagine\Image\Point;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageManipulationInterface;

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
     * @param ImageBlobInterface $image
     * @return ImageBlobInterface|ImagineImageBlob
     */
    public function applyTo(ImageBlobInterface $image): ImageBlobInterface
    {
        $imagine = $this->getImagineImage($image);
        $imagine = $imagine->crop(new Point($this->x, $this->y), new Box($this->width, $this->height));
        return ImagineImageBlob::fromImagineImage($imagine, $image->getMetadata());
    }
}
