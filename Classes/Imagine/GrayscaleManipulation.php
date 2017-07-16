<?php
namespace Kitsunet\ImageManipulation\Imagine;

use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;

/**
 *
 */
class GrayscaleManipulation
{
    use ManipulationHelperTrait;

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
