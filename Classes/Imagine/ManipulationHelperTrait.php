<?php
namespace Kitsunet\ImageManipulation\Imagine;

use Imagine\Image\ImageInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Neos\Flow\Annotations as Flow;

/**
 *
 */
trait ManipulationHelperTrait
{
    /**
     * @Flow\Inject
     * @var \Neos\Imagine\ImagineFactory
     */
    protected $_imagineFactory;

    /**
     * @param ImageBlobInterface $image
     * @return ImageInterface
     */
    protected function getImagineImage(ImageBlobInterface $image): ImageInterface
    {
        if ($image instanceof ImagineImageBlob) {
            return $image->getImagineImage();
        }

        $imagine = $this->_imagineFactory->create();
        return $imagine->read($image->getStream());
    }
}
