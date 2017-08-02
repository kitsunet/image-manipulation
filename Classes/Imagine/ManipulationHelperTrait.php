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
     * @var ImageBlobFactory
     */
    protected $_imagineBlobFactory;

    /**
     * @param ImageBlobInterface $image
     * @return ImagineImageBlob
     */
    protected function upgradeToImagineBlob(ImageBlobInterface $image): ImagineImageBlob
    {
        if ($image instanceof ImagineImageBlob) {
            return $image;
        }

        $this->_imagineBlobFactory->create($image->getStream(), clone $image->getMetadata());
    }
}
