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
