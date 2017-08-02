<?php
namespace Kitsunet\ImageManipulation\Description;

use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\ImageManipulationInterface;

/**
 * Contract for a ManipulationDescription to Mainpulation mapper.
 */
interface DescriptionMappingServiceInterface
{
    /**
     * @param ManipulationDescriptionInterface[] $descriptionStack
     * @param ImageBlobInterface $imageBlob
     * @return ImageManipulationInterface[]
     */
    public function mapDescriptionsToManipulations(array $descriptionStack, ImageBlobInterface $imageBlob): array;
}
