<?php
namespace Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description;

use Kitsunet\ImageManipulation\ImageBlob\Box;
use Kitsunet\ImageManipulation\ImageBlob\BoxInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\Point;

/**
 *  Receives the following options:
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
     * Inset ratio mode: If an image is attempted to get scaled with the size of both edges stated, using this mode will scale it to the lower of both edges.
     * Consider an image of 320/480 being scaled to 50/50: because aspect ratio wouldn't get hurt, the target image size will become 33/50.
     */
    const RATIOMODE_INSET = 'inset';

    /**
     * Outbound ratio mode: If an image is attempted to get scaled with the size of both edges stated, using this mode will scale the image and crop it.
     * Consider an image of 320/480 being scaled to 50/50: the image will be scaled to height 50, then centered and cropped so the width will also be 50.
     */
    const RATIOMODE_OUTBOUND = 'outbound';

    /**
     * @var array
     */
    protected $options;

    /**
     * GenericDescription constructor.
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
    public function getType(): string
    {
        return 'complexResize';
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Decompose complex operation into simple operations.
     *
     * @param ImageBlobInterface $imageBlob
     * @return ManipulationDescriptionInterface[]
     */
    public function decompose(ImageBlobInterface $imageBlob): array
    {
        $imageSize = $imageBlob->getSize();
        $ratioMode = $this->options['ratioMode'] ?: static::RATIOMODE_INSET;
        if ($ratioMode !== static::RATIOMODE_INSET &&
            $ratioMode !== static::RATIOMODE_OUTBOUND
        ) {
            throw new \InvalidArgumentException('Invalid mode specified');
        }

        $requestedDimensions = $this->calculateDimensions($imageSize);
        $resizeDimensions = $requestedDimensions;
        if ($ratioMode === static::RATIOMODE_OUTBOUND) {
            $resizeDimensions = $this->calculateOutboundScalingDimensions($imageSize, $requestedDimensions);
        }

        $manipulationDescriptions = [];
        $manipulationDescriptions[] = ResizeDescription::toDimensions($resizeDimensions);

        if ($ratioMode === static::RATIOMODE_OUTBOUND) {
            $manipulationDescriptions[] = CropDescription::withFocusAndSize(
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
    protected function calculateDimensions(BoxInterface $originalDimensions): BoxInterface
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
    protected function calculateWithFixedDimensions(BoxInterface $originalDimensions, int $requestedWidth, int $requestedHeight): BoxInterface
    {
        $ratioMode = $this->options['ratioMode'] ?: static::RATIOMODE_INSET;

        if ($ratioMode === static::RATIOMODE_OUTBOUND) {
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
    protected function calculateOutboundBox(BoxInterface $originalDimensions, int $requestedWidth, int $requestedHeight): BoxInterface
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
    protected function calculateScalingToWidth(BoxInterface $originalDimensions, int $requestedWidth): BoxInterface
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
    protected function calculateScalingToHeight(BoxInterface $originalDimensions, int $requestedHeight): BoxInterface
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
    protected function calculateOutboundScalingDimensions(BoxInterface $imageSize, BoxInterface $requestedDimensions): BoxInterface
    {
        $ratios = [
            $requestedDimensions->getWidth() / $imageSize->getWidth(),
            $requestedDimensions->getHeight() / $imageSize->getHeight()
        ];

        return $imageSize->scale(max($ratios));
    }
}
