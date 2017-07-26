<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

/**
 * Class Box
 *
 */
class Box implements BoxInterface
{
    /**
     * @var integer
     */
    private $width;

    /**
     * @var integer
     */
    private $height;

    /**
     * Constructs the Box with given width and height
     *
     * @param int $width
     * @param integer $height
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(int $width, int $height)
    {
        if ($height < 1 || $width < 1) {
            throw new \InvalidArgumentException(sprintf('Length of either side cannot be 0 or negative, current size is %sx%s',
                $width, $height), 1465382619);
        }

        $this->width = (integer)$width;
        $this->height = (integer)$height;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @param float $ratio
     * @return BoxInterface
     */
    public function scale(float $ratio): BoxInterface
    {
        return new static(max(round($ratio * $this->width), 1), max(round($ratio * $this->height), 1));
    }

    /**
     * @param int $size
     * @return BoxInterface
     */
    public function increase(int $size): BoxInterface
    {
        return new static((integer)$size + $this->width, (integer)$size + $this->height);
    }

    /**
     * @param BoxInterface $box
     * @param Point|null $start
     * @return bool
     */
    public function contains(BoxInterface $box, Point $start = null): bool
    {
        $start = $start ? $start : new Point(0, 0);

        return $start->in($this) && $this->width >= $box->getWidth() + $start->getX() && $this->height >= $box->getHeight() + $start->getY();
    }

    /**
     * @return int
     */
    public function area(): int
    {
        return $this->width * $this->height;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return sprintf('%dx%d px', $this->width, $this->height);
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
     * @return BoxInterface
     */
    public function widen(float $width): BoxInterface
    {
        return $this->scale($width / $this->width);
    }

    /**
     * @param float $height
     * @return BoxInterface
     */
    public function heighten(float $height): BoxInterface
    {
        return $this->scale($height / $this->height);
    }
}
