<?php
namespace Kitsunet\ImageManipulation\Imagine;

use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\ImageManipulationInterface;

/**
 *
 */
class GrayscaleManipulation implements ImageManipulationInterface
{
    use ManipulationHelperTrait;

    /**
     * @param array $options
     * @return static
     */
    public static function fromOptions(array $options): self
    {
        return new static();
    }

    /**
     * @param ImageBlobInterface $image
     * @return ImageBlobInterface|ImagineImageBlob
     */
    public function applyTo(ImageBlobInterface $image): ImageBlobInterface
    {
        if (!$image instanceof ImagineImageBlob) {
            $image = $this->upgradeToImagineBlob($image);
        }

        $imagine = $image->getImagineImage();
        $imagine->effects()->grayscale();
        return ImagineImageBlob::fromImagineImage($imagine, $image->getMetadata());
    }
}
