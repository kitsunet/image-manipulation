<?php
namespace Kitsunet\ImageManipulation\Imagine;

use Imagine\Image\ImageInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Neos\Imagine\ImagineFactory;

/**
 *
 */
trait ManipulationHelperTrait
{
    /**
     * @param ImageBlobInterface $image
     * @return ImageInterface
     */
    protected function getImagineImage(ImageBlobInterface $image): ImageInterface
    {
        if ($image instanceof ImagineImageBlob) {
            return $image->getImagineImage();
        }

        $factory = new ImagineFactory();
        $imagine = $factory->create();

        return $imagine->read($image->getStream());
    }
}
