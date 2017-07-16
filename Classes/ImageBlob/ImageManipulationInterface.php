<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

/**
 * Describes an image manipulation.
 */
interface ImageManipulationInterface
{
    /**
     *  Take the given image and returns *a new*
     *  image with the manipulation added to it.
     *
     * @param ImageBlobInterface $image
     * @return ImageBlobInterface
     */
    public function applyTo(ImageBlobInterface $image): ImageBlobInterface;
}
