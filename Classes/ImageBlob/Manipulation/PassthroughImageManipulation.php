<?php
namespace Kitsunet\ImageManipulation\ImageBlob\Manipulation;

use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\ManipulationDescriptionInterface;

/**
 *
 */
class PassthroughImageManipulation implements ImageManipulationInterface
{
    /**
     * @param ManipulationDescriptionInterface $description
     * @return static
     */
    public static function fromDescription(ManipulationDescriptionInterface $description)
    {
        return new static();
    }

    /**
     * @param ImageBlobInterface $image
     * @return ImageBlobInterface
     */
    public function applyTo(ImageBlobInterface $image): ImageBlobInterface
    {
        return $image;
    }
}
