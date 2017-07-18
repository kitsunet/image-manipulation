<?php
namespace Kitsunet\ImageManipulation\Imagine;

use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\CropManipulationDescription;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\DecomposableInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\GenericManipulationDescription;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\ManipulationDescriptionInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\ResizeManipulationDescription;
use Neos\Flow\Annotations as Flow;

/**
 *
 */
class DescriptionToManipulationConverter
{
    /**
     * @var ImageBlobInterface
     */
    protected $imageBlob;

    /**
     * @var ManipulationDescriptionInterface[]
     */
    protected $descriptionStack;

    /**
     * DescriptionToManipulationConverter constructor.
     *
     * @param ManipulationDescriptionInterface[] $descriptionStack
     * @param ImageBlobInterface $imageBlob
     */
    public function __construct(array $descriptionStack, ImageBlobInterface $imageBlob)
    {
        $this->imageBlob = $imageBlob;
        $this->descriptionStack = $descriptionStack;
    }

    /**
     * @return array
     */
    public function getManipulationStack()
    {
        return $this->convertDescriptionsToManipulations($this->descriptionStack, $this->imageBlob);
    }

    /**
     * @param array $descriptionStack
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
        if ($description instanceof DecomposableInterface) {
            $manipulationDescriptions = $description->decompose($imageBlob->getSize());
            return $this->convertDescriptionsToManipulations($manipulationDescriptions, $imageBlob);
        }

        if ($description instanceof CropManipulationDescription) {
            return [CropManipulation::fromDescription($description)];
        }

        if ($description instanceof ResizeManipulationDescription) {
            return [ResizeManipulation::fromDescription($description)];
        }

        if ($description instanceof GenericManipulationDescription) {
            switch ($description->getType()) {
                case 'grayscale':
                    return [new GrayscaleManipulation];
                    break;
            }
        }

        return [];
    }
}
