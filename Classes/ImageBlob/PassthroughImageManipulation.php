<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

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
