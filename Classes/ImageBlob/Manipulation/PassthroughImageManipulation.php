<?php
namespace Kitsunet\ImageManipulation\ImageBlob\Manipulation;

use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;

/**
 *
 */
class PassthroughImageManipulation implements ImageManipulationInterface
{
    /**
     * @param ImageBlobInterface $image
     * @return ImageBlobInterface
     */
    public function applyTo(ImageBlobInterface $image): ImageBlobInterface
    {
        return $image;
    }
}
