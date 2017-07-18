<?php
namespace Kitsunet\ImageManipulation\Media;

use Kitsunet\ImageManipulation\ImageBlob\Box;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\CropManipulationDescription;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\GenericManipulationDescription;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\ManipulationDescriptionInterface;
use Kitsunet\ImageManipulation\ImageBlob\Point;
use Neos\Flow\Annotations as Flow;
use Neos\Media\Domain\Model\Adjustment\CropImageAdjustment;
use Neos\Media\Domain\Model\Adjustment\ImageAdjustmentInterface;
use Neos\Media\Domain\Model\Adjustment\ResizeImageAdjustment;

/**
 * @Flow\Scope("singleton")
 */
class ManipulationDescriptionFactory
{
    /**
     * @param ImageAdjustmentInterface[] $adjustments
     * @return ManipulationDescriptionInterface[]
     */
    public function convertAdjustments(array $adjustments)
    {
        return array_map([$this, 'convertAdjustment'], $adjustments);
    }

    /**
     * @param ImageAdjustmentInterface $adjustment
     * @return ManipulationDescriptionInterface
     */
    public function convertAdjustment(ImageAdjustmentInterface $adjustment)
    {
        switch (get_class($adjustment)) {
            case ResizeImageAdjustment::class:
                $converter = new ResizeAdjustmentConverter($adjustment);
                return $converter->getDescription();
                break;
            case CropImageAdjustment::class:
                return CropManipulationDescription::withFocusAndSize(
                    new Point($adjustment->getX(), $adjustment->getY()),
                    new Box($adjustment->getWidth(), $adjustment->getHeight())
                );
                break;
            default:
                return new GenericManipulationDescription('passthrough', []);
        }
    }
}
