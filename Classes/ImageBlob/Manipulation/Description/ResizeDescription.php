<?php
namespace Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description;

use Kitsunet\ImageManipulation\ImageBlob\BoxInterface;

/**
 * Describes a resize manipulation.
 * If either width or height is 0 it means this
 * resize is proportionally scaling to the given dimension.
 * With both dimensions given you might get a skewed image.
 */
class ResizeDescription implements ManipulationDescriptionInterface
{
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
        return ManipulationDescriptionInterface::TYPE_RESIZE;
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
