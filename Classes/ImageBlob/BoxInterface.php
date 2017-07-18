<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

/**
 * Class Box
 *
 */
interface BoxInterface
{
    /**
     * {@inheritdoc}
     */
    public function getWidth();

    /**
     * {@inheritdoc}
     */
    public function getHeight();

    /**
     * {@inheritdoc}
     */
    public function scale($ratio);

    /**
     * {@inheritdoc}
     */
    public function increase($size);

    /**
     * {@inheritdoc}
     */
    public function contains(Box $box, Point $start = null);

    /**
     * {@inheritdoc}
     */
    public function square();

    /**
     * {@inheritdoc}
     */
    public function widen($width);

    /**
     * {@inheritdoc}
     */
    public function heighten($height);

    /**
     * @return array with width and height keys
     */
    public function toArray();
}
