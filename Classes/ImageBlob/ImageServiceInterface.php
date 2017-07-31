<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

/**
 * Contract for a service to manipulate ImageBlobs.
 *
 * @api
 */
interface ImageServiceInterface
{
    /**
     * @param ImageBlobInterface $blob
     * @param ManipulationDescriptionInterface[] $manipulationDescriptions
     * @return ImageBlobInterface
     */
    public function process(ImageBlobInterface $blob, array $manipulationDescriptions);

    /**
     * @param ImageBlobInterface $blob
     * @param ImageManipulationInterface[] $manipulations
     * @return ImageBlobInterface
     */
    public function manipulate(ImageBlobInterface $blob, array $manipulations);
}
