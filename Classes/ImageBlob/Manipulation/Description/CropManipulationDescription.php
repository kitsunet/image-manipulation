<?php
namespace Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description;

use Kitsunet\ImageManipulation\ImageBlob\BoxInterface;
use Kitsunet\ImageManipulation\ImageBlob\Point;

/**
 * Describes a crop manipulation.
 * Options will return x, y coordinates as
 * top left corner and width and height as dimensions of the resulting
 * cropped image.
 */
class CropManipulationDescription implements ManipulationDescriptionInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * CropManipulationDescription constructor.
     *
     * @param array $options
     */
    protected function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return ImageManipulationInterface::TYPE_CROP;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param Point $focusPoint
     * @param BoxInterface $size
     * @return static
     */
    public static function withFocusAndSize(Point $focusPoint, BoxInterface $size)
    {
        return new static(array_merge($focusPoint->toArray(), $size->toArray()));
    }
}
