<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

/**
 *
 */
class EmptyBox implements BoxInterface
{
    public function __construct() {

    }

    public function getWidth()
    {
        return 0;
    }

    public function getHeight()
    {
        return 0;
    }

    public function scale($ratio)
    {
        return new EmptyBox();
    }

    public function increase($size)
    {
        return new EmptyBox();
    }

    public function contains(Box $box, Point $start = null)
    {
        return false;
    }

    public function square()
    {
        return 0;
    }

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

    public function widen($width)
    {
        return new EmptyBox();
    }

    public function heighten($height)
    {
        return new EmptyBox();
    }
}
