<?php
namespace Kitsunet\ImageManipulation\Imagine;

use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\ManipulationDescriptionInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\ImageManipulationInterface;

/**
 *
 */
class GrayscaleManipulation implements ImageManipulationInterface
{
    use ManipulationHelperTrait;

    /**
     * @param ManipulationDescriptionInterface $description
     * @return static
     */
    public static function fromDescription(ManipulationDescriptionInterface $description): self
    {
        return new static();
    }

    /**
     * @param ImageBlobInterface $image
     * @return ImageBlobInterface|ImagineImageBlob
     */
    public function applyTo(ImageBlobInterface $image): ImageBlobInterface
    {
        $imagine = $this->getImagineImage($image);
        $imagine->effects()->grayscale();
        return ImagineImageBlob::fromImagineImage($imagine, $image->getMetadata());
    }
}
