<?php
namespace Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description;

use Kitsunet\ImageManipulation\ImageBlob\BoxInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\ImageManipulationInterface;

/**
 * Describes a resize manipulation.
 * If either width or height is 0 it means this
 * resize is proportionally scaling to the given dimension.
 * With both dimensions given you might get a skewed image.
 */
class ResizeManipulationDescription implements ManipulationDescriptionInterface
{
    /**
     * Inset ratio mode: If an image is attempted to get scaled with the size of both edges stated, using this mode will scale it to the lower of both edges.
     * Consider an image of 320/480 being scaled to 50/50: because aspect ratio wouldn't get hurt, the target image size will become 33/50.
     */
    const RATIOMODE_INSET = 'inset';

    /**
     * Outbound ratio mode: If an image is attempted to get scaled with the size of both edges stated, using this mode will scale the image and crop it.
     * Consider an image of 320/480 being scaled to 50/50: the image will be scaled to height 50, then centered and cropped so the width will also be 50.
     */
    const RATIOMODE_OUTBOUND = 'outbound';

    /**
     * @var array
     */
    protected $options;

    /**
     * ResizeManipulation constructor.
     *
     * @param int $width
     * @param int $height
     */
    protected function __construct(int $width = 0, int $height = 0)
    {
        $this->options = [
            'width' => $width,
            'height' => $height
        ];
    }

    /**
     * @return string
     */
    public function getType()
    {
        return ImageManipulationInterface::TYPE_RESIZE;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
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
     * @param BoxInterface $dimensions
     * @return static
     */
    public static function toDimensions(BoxInterface $dimensions)
    {
        return new static($dimensions->getWidth(), $dimensions->getHeight());
    }
}
