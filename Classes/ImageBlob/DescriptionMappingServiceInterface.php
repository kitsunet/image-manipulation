<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\ManipulationDescriptionInterface;
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
