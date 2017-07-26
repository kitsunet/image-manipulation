<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\ManipulationDescriptionInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\ImageManipulationInterface;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class DescriptionMappingService implements DescriptionMappingServiceInterface
{
    /**
     * @Flow\InjectConfiguration(package="Kitsunet.ImageManipulation.ImageBlob", path="typeBasedManipulationMapping")
     * @var array
     */
    protected $mappingConfiguration;

    /**
     * @param ManipulationDescriptionInterface[] $descriptionStack
     * @param ImageBlobInterface $imageBlob
     * @return ImageManipulationInterface[]
     */
    public function mapDescriptionsToManipulations(array $descriptionStack, ImageBlobInterface $imageBlob): array
    {
        $mapper = new ConfigurationBasedManipulationMapper($descriptionStack, $this->mappingConfiguration);
        return $mapper->getManipulationStackFor($imageBlob);
    }
}
