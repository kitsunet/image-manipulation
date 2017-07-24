<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

/**
 *
 */
class Point
{
    /**
     * @var int
     */
    protected $x;

    /**
     * @var int
     */
    protected $y;

    /**
     * Point constructor.
     *
     * @param int $x
     * @param int $y
     */
    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @param Box $rectangle
     * @return bool
     */
    public function in(Box $rectangle)
    {
        if ($this->x > $rectangle->getWidth()) {
            return false;
        }

        if ($this->y > $rectangle->getHeight()) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'x' => $this->getX(),
            'y' => $this->getY()
        ];
    }
}
