<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

/**
 * Definition of a geometrical point which is very useful for (pixel-based) images.
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
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * @param BoxInterface $rectangle
     * @return bool
     */
    public function in(BoxInterface $rectangle): bool
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
     * Returns an array with "x" and "y" keys.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'x' => $this->getX(),
            'y' => $this->getY()
        ];
    }
}
