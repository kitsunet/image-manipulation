<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\DecomposableInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\ManipulationDescriptionInterface;

/**
 *
 */
class ConfigurationBasedDescriptionMapper
{
    /**
     * @var ManipulationDescriptionInterface[]
     */
    protected $descriptionStack;

    /**
     * @var array
     */
    protected $configuration;

    /**
     * Converts descriptions to manipulations based on a preconfigured mapping.
     *
     * @param ManipulationDescriptionInterface[] $descriptionStack the
     * @param array $configuration A map "ManipulationDescriptionInterface implementation classname" => "ImageManipulationInterface implementation classname"
     */
    public function __construct(array $descriptionStack, array $configuration)
    {
        $this->descriptionStack = $descriptionStack;

        if (!isset($configuration['__fallback'])) {
            throw new \InvalidArgumentException('To use the ConfigurationBasedDescriptionMapper you must at least specify a "__fallback" mapping for any unkown descriptions.'. 1500823224884);
        }

        $this->configuration = $configuration;
    }

    /**
     * @param ImageBlobInterface $imageBlob
     * @return array
     */
    public function getManipulationStackFor(ImageBlobInterface $imageBlob)
    {
        return $this->convertDescriptionsToManipulations($this->descriptionStack, $imageBlob);
    }

    /**
     * @param ManipulationDescriptionInterface[] $descriptionStack
     * @param ImageBlobInterface $imageBlob
     * @return array
     */
    protected function convertDescriptionsToManipulations(array $descriptionStack, ImageBlobInterface $imageBlob)
    {
        return array_reduce($descriptionStack, function (array $manipulations, ManipulationDescriptionInterface $description) use ($imageBlob) {
            $manipulations = array_merge($manipulations, $this->convertDescriptionToManipulations($description, $imageBlob));

            return $manipulations;
        }, []);
    }

    /**
     * @param ManipulationDescriptionInterface $description
     * @param ImageBlobInterface $imageBlob
     * @return array
     */
    protected function convertDescriptionToManipulations(ManipulationDescriptionInterface $description, ImageBlobInterface $imageBlob)
    {
        if (isset($this->configuration[$description->getType()])) {
            $manipulationClassname = $this->configuration[$description->getType()];
            return [$manipulationClassname::fromDescription($description)];
        }

        if ($description instanceof DecomposableInterface) {
            $manipulationDescriptions = $description->decompose($imageBlob);
            return $this->convertDescriptionsToManipulations($manipulationDescriptions, $imageBlob);
        }

        $manipulationClassname = $this->configuration['__fallback'];
        return [$manipulationClassname::fromDescription($description)];
    }
}
