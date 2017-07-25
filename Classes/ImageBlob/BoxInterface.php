<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

/**
 * Interface for a simple box with width and height.
 *
 */
interface BoxInterface
{
    /**
     * @return int
     */
    public function getWidth();

    /**
     * @return int
     */
    public function getHeight();

    /**
     * Multiplies the size by given ratio and returns a new box.
     *
     * @param float $ratio
     * @return BoxInterface
     */
    public function scale($ratio);

    /**
     * Adds the given size to width and height and returns a new box.
     *
     * @param int $size
     * @return static
     */
    public function increase($size);

    /**
     * Is the given box inside this one?
     * Possibly positioned off the top/left corner by the given point.
     *
     * @param BoxInterface $box
     * @param Point $start
     * @return bool
     */
    public function contains(BoxInterface $box, Point $start = null);

    /**
     * The area of this box.
     *
     * @return int
     */
    public function area();

    /**
     * Proportionally widen the box by this ratio.
     *
     * @param float $width
     * @return static
     */
    public function widen($width);

    /**
     * Proportionally heighten the box by this ratio.
     *
     * @param float $height
     * @return static
     */
    public function heighten($height);

    /**
     * @return array with "width" and "height" keys
     */
    public function toArray();
}
