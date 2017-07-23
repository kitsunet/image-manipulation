<?php
namespace Kitsunet\ImageManipulation\Media;

use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\ComplexResizeDescription;
use Neos\Media\Domain\Model\Adjustment\ResizeImageAdjustment;

/**
 *
 */
class ResizeAdjustmentConverter
{
    /**
     * @var ResizeImageAdjustment
     */
    protected $resizeAdjustment;

    public function __construct(ResizeImageAdjustment $resizeAdjustment)
    {
        $this->resizeAdjustment = $resizeAdjustment;
    }

    /**
     * @return array
     */
    public function getDescription()
    {
        return new ComplexResizeDescription([
            'width' => $this->resizeAdjustment->getWidth(),
            'height' => $this->resizeAdjustment->getHeight(),
            'maximumWidth' => $this->resizeAdjustment->getMaximumWidth(),
            'maximumHeight' => $this->resizeAdjustment->getMaximumHeight(),
            'ratioMode' => $this->resizeAdjustment->getRatioMode(),
            'allowUpscaling' => $this->resizeAdjustment->getAllowUpScaling()
        ]);
    }
}
