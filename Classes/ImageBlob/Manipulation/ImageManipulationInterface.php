<?php
namespace Kitsunet\ImageManipulation\ImageBlob\Manipulation;

use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;

/**
 * An image manipulation returning a new image blob.
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

    /**
     * @param array $options
     * @return static
     * @throws \InvalidArgumentException If description cannot be used to create this manipulation.
     */
    public static function fromOptions(array $options);
}
