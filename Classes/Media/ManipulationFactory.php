<?php
namespace Kitsunet\ImageManipulation\Media;

use Kitsunet\ImageManipulation\ImageBlob\Manipulation\ImageManipulationInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\LoggingPassthroughImageManipulation;
use Neos\Flow\Annotations as Flow;
use Neos\Media\Domain\Model\Adjustment\ImageAdjustmentInterface;
use Neos\Utility\ObjectAccess;

/**
 * @Flow\Scope("singleton")
 */
class ManipulationFactory
{
    /**
     * @Flow\InjectConfiguration(package="Neos.Media", path="imageBlob.adjustmentMapping")
     * @var array
     */
    protected $adjustmentMapping;

    /**
     * @param ImageAdjustmentInterface[] $adjustments
     * @return ImageManipulationInterface[]
     */
    public function convertAdjustments(array $adjustments): array
    {
        return array_map([$this, 'convertAdjustment'], $adjustments);
    }

    /**
     * @param ImageAdjustmentInterface $adjustment
     * @return ImageManipulationInterface
     */
    public function convertAdjustment(ImageAdjustmentInterface $adjustment): ImageManipulationInterface
    {
        $options = ObjectAccess::getGettableProperties($adjustment);
        $manipulationClassName = $this->adjustmentMapping[get_class($adjustment)]['class'] ?? LoggingPassthroughImageManipulation::class;

        if (!is_subclass_of($manipulationClassName, ImageManipulationInterface::class, true)) {
            throw new \InvalidArgumentException(sprintf('The class "%s" does not implement the "%s".', $manipulationClassName, ImageManipulationInterface::class), 1501693388244);
        }

        return $manipulationClassName::fromOptions($options);
    }
}
