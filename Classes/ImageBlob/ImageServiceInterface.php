<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

use Kitsunet\ImageManipulation\ImageBlob\Manipulation\ImageManipulationInterface;

/**
 * Contract for a service to manipulate ImageBlobs.
 *
 * @api
 */
interface ImageServiceInterface
{
    /**
     * @param ImageBlobInterface $blob
     * @param ImageManipulationInterface[] $manipulations
     * @return ImageBlobInterface
     */
    public function manipulate(ImageBlobInterface $blob, array $manipulations);
}
