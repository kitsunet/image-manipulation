<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\DecomposableInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\ManipulationDescriptionInterface;

/**
 * A simple mapper using a configuation of description type => manipulation class name mappings.
 */
class ConfigurationBasedManipulationMapper
{
    /**
     * @var ManipulationDescriptionInterface[]
     */
    protected $descriptionStack;

    /**
     * The mapping mappingConfiguration.
     *
     * @var array
     */
    protected $mappingConfiguration;

    /**
     * Converts descriptions to manipulations based on a preconfigured mapping.
     *
     * @param ManipulationDescriptionInterface[] $descriptionStack The descriptions that are to be converted.
     * @param array $configuration A map "ManipulationDescriptionInterface implementation classname" => "ImageManipulationInterface implementation classname"
     */
    public function __construct(array $descriptionStack, array $configuration)
    {
        $this->descriptionStack = $descriptionStack;

        if (!isset($configuration['__fallback'])) {
            throw new \InvalidArgumentException('To use the ConfigurationBasedManipulationMapper you must at least specify a "__fallback" mapping for any unknown descriptions.'. 1500823224884);
        }

        $this->mappingConfiguration = $configuration;
    }

    /**
     * Get a stack of manipulations for the given ImageBlob and the descriptions bound to this instance.
     *
     * @param ImageBlobInterface $imageBlob
     * @return array
     */
    public function getManipulationStackFor(ImageBlobInterface $imageBlob): array
    {
        return $this->convertDescriptionsToManipulations($this->descriptionStack, $imageBlob);
    }

    /**
     * Implements the reduce logic that converts descriptions to manipulations.
     *
     * @param ManipulationDescriptionInterface[] $descriptionStack
     * @param ImageBlobInterface $imageBlob
     * @return array
     */
    protected function convertDescriptionsToManipulations(array $descriptionStack, ImageBlobInterface $imageBlob): array
    {
        return array_reduce($descriptionStack, function (array $manipulations, ManipulationDescriptionInterface $description) use ($imageBlob) {
            $manipulations = array_merge($manipulations, $this->convertDescriptionToManipulations($description, $imageBlob));

            return $manipulations;
        }, []);
    }

    /**
     * Convert a single description to an array of manipulations.
     *
     * @param ManipulationDescriptionInterface $description
     * @param ImageBlobInterface $imageBlob
     * @return array
     */
    protected function convertDescriptionToManipulations(ManipulationDescriptionInterface $description, ImageBlobInterface $imageBlob): array
    {
        if (isset($this->mappingConfiguration[$description->getType()])) {
            $manipulationClassname = $this->mappingConfiguration[$description->getType()];
            return [$manipulationClassname::fromDescription($description)];
        }

        if ($description instanceof DecomposableInterface) {
            $manipulationDescriptions = $description->decompose($imageBlob);
            return $this->convertDescriptionsToManipulations($manipulationDescriptions, $imageBlob);
        }

        $manipulationClassname = $this->mappingConfiguration['__fallback'];
        return [$manipulationClassname::fromDescription($description)];
    }
}
