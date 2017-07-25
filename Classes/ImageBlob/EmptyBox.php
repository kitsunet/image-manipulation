<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

/**
 * An empty box as a special case.
 */
class EmptyBox implements BoxInterface
{
    /**
     * EmptyBox constructor
     */
    public function __construct() {

    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return 0;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return 0;
    }

    /**
     * @param int $ratio
     * @return EmptyBox
     */
    public function scale($ratio)
    {
        return new EmptyBox();
    }

    /**
     * @param int $size
     * @return Box
     */
    public function increase($size)
    {
        return new Box($size, $size);
    }

    /**
     * @param BoxInterface $box
     * @param Point|null $start
     * @return bool
     */
    public function contains(BoxInterface $box, Point $start = null)
    {
        return false;
    }

    /**
     * @return int
     */
    public function area()
    {
        return 0;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'empty box';
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'width' => $this->getWidth(),
            'height' => $this->getHeight()
        ];
    }

    /**
     * @param int $width
     * @return EmptyBox
     */
    public function widen($width)
    {
        return new EmptyBox();
    }

    /**
     * @param int $height
     * @return EmptyBox
     */
    public function heighten($height)
    {
        return new EmptyBox();
    }
}
