<?php
namespace Kitsunet\ImageManipulation\Imagine;

use Imagine\Image\ImageInterface;
use Kitsunet\ImageManipulation\ImageBlob\BoxInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\ImageManipulationInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Manipulator;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\ResizeCropManipulationHelperTrait;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\ResizeCropManipulationInterface;
use Kitsunet\ImageManipulation\ImageBlob\Point;

/**
 *
 */
class ResizeCropManipulation implements ImageManipulationInterface, ResizeCropManipulationInterface
{
    use ResizeCropManipulationHelperTrait;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var string
     */
    protected $filter;

    /**
     * ResizeCropManipulation constructor.
     *
     * @param array $options
     * @param string $filter
     */
    public function __construct(array $options, string $filter = ImageInterface::FILTER_UNDEFINED)
    {
        $this->options = $options;
        $this->filter = $filter;
    }

    /**
     * @param ImageBlobInterface $image
     * @return ImageBlobInterface
     */
    public function applyTo(ImageBlobInterface $image): ImageBlobInterface
    {
        $manipulator = $this->getSubManipulations($image);
        return $manipulator->applyTo($image);
    }

    /**
     * Create the specific resize implementation based on the box representing width and height.
     *
     * @param BoxInterface $resizeDimensions
     * @return ResizeManipulation
     */
    protected function createResizeManipulation(BoxInterface $resizeDimensions)
    {
        return ResizeManipulation::toBox($resizeDimensions, $this->filter);
    }

    /**
     * Create the specific crop implmentation based on a top left point and a box representing width and height.
     *
     * @param Point $point
     * @param BoxInterface $requestedDimensions
     * @return CropManipulation
     */
    protected function createCropManipulation(Point $point, BoxInterface $requestedDimensions)
    {
        return CropManipulation::fromPointAndBox($point, $requestedDimensions);
    }

    /**
     * @param array $options
     * @return static
     */
    public static function fromOptions(array $options)
    {
        return new static($options);
    }
}
