<?php
namespace Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description;

use Kitsunet\ImageManipulation\ImageBlob\Box;
use Kitsunet\ImageManipulation\ImageBlob\BoxInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\Point;

/**
 *
 * - width
 * - height
 * - maximumWidth
 * - maximumHeight
 * - ratioMode
 * - allowUpscaling
 */
class ComplexResizeDescription implements ManipulationDescriptionInterface, DecomposableInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * GenericManipulationDescription constructor.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'complexResize';
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Decompose complex operation into simple operations.
     *
     * @param ImageBlobInterface $imageBlob
     * @return ManipulationDescriptionInterface[]
     */
    public function decompose(ImageBlobInterface $imageBlob)
    {
        $imageSize = $imageBlob->getSize();
        $ratioMode = $this->options['ratioMode'];
        if ($ratioMode !== ResizeManipulationDescription::RATIOMODE_INSET &&
            $ratioMode !== ResizeManipulationDescription::RATIOMODE_OUTBOUND
        ) {
            throw new \InvalidArgumentException('Invalid mode specified');
        }

        $requestedDimensions = $this->calculateDimensions($imageSize);
        $resizeDimensions = $requestedDimensions;
        if ($ratioMode === ResizeManipulationDescription::RATIOMODE_OUTBOUND) {
            $resizeDimensions = $this->calculateOutboundScalingDimensions($imageSize, $requestedDimensions);
        }

        $manipulationDescriptions = [];
        $manipulationDescriptions[] = ResizeManipulationDescription::toDimensions($resizeDimensions);

        if ($ratioMode === ResizeManipulationDescription::RATIOMODE_OUTBOUND) {
            $manipulationDescriptions[] = CropManipulationDescription::withFocusAndSize(
                new Point(
                    max(0, round(($resizeDimensions->getWidth() - $requestedDimensions->getWidth()) / 2)),
                    max(0, round(($resizeDimensions->getHeight() - $requestedDimensions->getHeight()) / 2))
                ),
                $requestedDimensions
            );
        }

        return $manipulationDescriptions;
    }

    /**
     * Calculates and returns the dimensions the image should have according all parameters set
     * in this adjustment.
     *
     * @param BoxInterface $originalDimensions Dimensions of the unadjusted image
     * @return BoxInterface
     */
    protected function calculateDimensions(BoxInterface $originalDimensions)
    {
        /** @var BoxInterface $newDimensions */
        $newDimensions = clone $originalDimensions;
        $width = $this->options['width'];
        $height = $this->options['height'];
        $maximumWidth = $this->options['maximumWidth'];
        $maximumHeight = $this->options['maximumHeight'];

        switch (true) {
            // height and width are set explicitly:
            case ($width !== null && $height !== null):
                $newDimensions = $this->calculateWithFixedDimensions($originalDimensions, $width, $height);
                break;
            // only width is set explicitly:
            case ($width !== null):
                $newDimensions = $this->calculateScalingToWidth($originalDimensions, $width);
                break;
            // only height is set explicitly:
            case ($height !== null):
                $newDimensions = $this->calculateScalingToHeight($originalDimensions, $height);
                break;
        }

        // We apply maximum dimensions and scale the new dimensions proportionally down to fit into the maximum.
        if ($maximumWidth !== null && $newDimensions->getWidth() > $maximumWidth) {
            $newDimensions = $newDimensions->widen($maximumWidth);
        }

        if ($maximumHeight !== null && $newDimensions->getHeight() > $maximumHeight) {
            $newDimensions = $newDimensions->heighten($maximumHeight);
        }

        return $newDimensions;
    }

    /**
     * @param BoxInterface $originalDimensions
     * @param int $requestedWidth
     * @param int $requestedHeight
     * @return BoxInterface
     */
    protected function calculateWithFixedDimensions(BoxInterface $originalDimensions, $requestedWidth, $requestedHeight)
    {
        $ratioMode = $this->options['ratioMode'] ?: ResizeManipulationDescription::RATIOMODE_INSET;

        if ($ratioMode === ResizeManipulationDescription::RATIOMODE_OUTBOUND) {
            return $this->calculateOutboundBox($originalDimensions, $requestedWidth, $requestedHeight);
        }

        $newDimensions = clone $originalDimensions;

        $ratios = [
            $requestedWidth / $originalDimensions->getWidth(),
            $requestedHeight / $originalDimensions->getHeight()
        ];

        $ratio = min($ratios);
        $newDimensions = $newDimensions->scale($ratio);

        if ($this->options['allowUpscaling'] === false && $originalDimensions->contains($newDimensions) === false) {
            return clone $originalDimensions;
        }

        return $newDimensions;
    }

    /**
     * Calculate the final dimensions for an outbound box. usually exactly the requested width and height unless that
     * would require upscaling and it is not allowed.
     *
     * @param BoxInterface $originalDimensions
     * @param int $requestedWidth
     * @param int $requestedHeight
     * @return BoxInterface
     */
    protected function calculateOutboundBox(BoxInterface $originalDimensions, $requestedWidth, $requestedHeight)
    {
        $newDimensions = new Box($requestedWidth, $requestedHeight);

        if ($this->options['allowUpscaling'] === true || $originalDimensions->contains($newDimensions) === true) {
            return $newDimensions;
        }

        // We need to make sure that the new dimensions are such that no upscaling is needed.
        $ratios = [
            $originalDimensions->getWidth() / $requestedWidth,
            $originalDimensions->getHeight() / $requestedHeight
        ];

        $ratio = min($ratios);
        $newDimensions = $newDimensions->scale($ratio);

        return $newDimensions;
    }

    /**
     * Calculates new dimensions with a requested width applied. Takes upscaling into consideration.
     *
     * @param BoxInterface $originalDimensions
     * @param int $requestedWidth
     * @return BoxInterface
     */
    protected function calculateScalingToWidth(BoxInterface $originalDimensions, $requestedWidth)
    {
        if ($this->options['allowUpscaling'] === false && $requestedWidth >= $originalDimensions->getWidth()) {
            return $originalDimensions;
        }

        $newDimensions = clone $originalDimensions;
        $newDimensions = $newDimensions->widen($requestedWidth);

        return $newDimensions;
    }

    /**
     * Calculates new dimensions with a requested height applied. Takes upscaling into consideration.
     *
     * @param BoxInterface $originalDimensions
     * @param int $requestedHeight
     * @return BoxInterface
     */
    protected function calculateScalingToHeight(BoxInterface $originalDimensions, $requestedHeight)
    {
        if ($this->options['allowUpscaling'] === false && $requestedHeight >= $originalDimensions->getHeight()) {
            return $originalDimensions;
        }

        $newDimensions = clone $originalDimensions;
        $newDimensions = $newDimensions->heighten($requestedHeight);

        return $newDimensions;
    }

    /**
     * Calculates a resize dimension box that allows for outbound resize.
     * The scaled image will be bigger than the requested dimensions in one dimension and then cropped.
     *
     * @param BoxInterface $imageSize
     * @param BoxInterface $requestedDimensions
     * @return BoxInterface
     */
    protected function calculateOutboundScalingDimensions(BoxInterface $imageSize, BoxInterface $requestedDimensions)
    {
        $ratios = [
            $requestedDimensions->getWidth() / $imageSize->getWidth(),
            $requestedDimensions->getHeight() / $imageSize->getHeight()
        ];

        return $imageSize->scale(max($ratios));
    }
}
