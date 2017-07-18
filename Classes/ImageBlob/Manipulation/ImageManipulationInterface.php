<?php
namespace Kitsunet\ImageManipulation\ImageBlob\Manipulation;

use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;

/**
 * Describes an image manipulation.
 */
interface ImageManipulationInterface
{
    const TYPE_PASSTHROUGH = 'passthrough';
    const TYPE_CROP = 'crop';
    const TYPE_RESIZE = 'resize';
    const TYPE_GRAYSCALE = 'grayscale';

    /**
     *  Take the given image and returns *a new*
     *  image with the manipulation added to it.
     *
     * @param ImageBlobInterface $image
     * @return ImageBlobInterface
     */
    public function applyTo(ImageBlobInterface $image): ImageBlobInterface;
}
