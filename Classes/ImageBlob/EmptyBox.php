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
    public function getWidth(): int
    {
        return 0;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return 0;
    }

    /**
     * @param float $ratio
     * @return EmptyBox
     */
    public function scale(float $ratio): BoxInterface
    {
        return new EmptyBox();
    }

    /**
     * @param int $size
     * @return BoxInterface
     */
    public function increase(int $size): BoxInterface
    {
        return new Box($size, $size);
    }

    /**
     * @param BoxInterface $box
     * @param Point|null $start
     * @return bool
     */
    public function contains(BoxInterface $box, Point $start = null): bool
    {
        return false;
    }

    /**
     * @return int
     */
    public function area(): int
    {
        return 0;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'empty box';
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'width' => $this->getWidth(),
            'height' => $this->getHeight()
        ];
    }

    /**
     * @param float $width
     * @return EmptyBox
     */
    public function widen(float $width): BoxInterface
    {
        return new EmptyBox();
    }

    /**
     * @param float $height
     * @return EmptyBox
     */
    public function heighten(float $height): BoxInterface
    {
        return new EmptyBox();
    }
}
