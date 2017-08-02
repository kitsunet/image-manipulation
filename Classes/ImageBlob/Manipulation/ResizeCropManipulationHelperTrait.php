<?php
namespace Kitsunet\ImageManipulation\ImageBlob\Manipulation;

use Kitsunet\ImageManipulation\ImageBlob\BoxInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\Point;

/**
 *
 */
trait ResizeCropManipulationHelperTrait
{
    /**
     * Decompose complex operation into simple operations.
     *
     * @param ImageBlobInterface $imageBlob
     * @return Manipulator
     */
    protected function getSubManipulations(ImageBlobInterface $imageBlob): Manipulator
    {
        $imageSize = $imageBlob->getSize();
        $ratioMode = $this->options['ratioMode'] ?: ResizeCropManipulationInterface::RATIOMODE_INSET;
        if ($ratioMode !== ResizeCropManipulationInterface::RATIOMODE_INSET &&
            $ratioMode !== ResizeCropManipulationInterface::RATIOMODE_OUTBOUND
        ) {
            throw new \InvalidArgumentException('Invalid mode specified', 1501684163544);
        }

        $requestedDimensions = $this->calculateDimensions($imageSize);
        $resizeDimensions = $requestedDimensions;
        if ($ratioMode === ResizeCropManipulationInterface::RATIOMODE_OUTBOUND) {
            $resizeDimensions = $this->calculateOutboundScalingDimensions($imageSize, $requestedDimensions);
        }

        $manipulations = [];
        $manipulations[] = $this->createResizeManipulation($resizeDimensions);

        if ($ratioMode === ResizeCropManipulationInterface::RATIOMODE_OUTBOUND) {
            $manipulations[] = $this->createCropManipulation(new Point(
                max(0, round(($resizeDimensions->getWidth() - $requestedDimensions->getWidth()) / 2)),
                max(0, round(($resizeDimensions->getHeight() - $requestedDimensions->getHeight()) / 2))
            ), $requestedDimensions);
        }

        $manipulator = new Manipulator($manipulations);
        return $manipulator;
    }

    /**
     * Create the specific resize implementation based on the box representing width and height.
     */
    protected function createResizeManipulation(BoxInterface $resizeDimensions)
    {
        throw new \BadMethodCallException(sprintf('Users of this trait need to implement the "createResizeManipulation" method but "%s" does not.', get_class($this)), 1501685109418);
    }

    /**
     * Create the specific crop implmentation based on a top left point and a box representing width and height.
     */
    protected function createCropManipulation(Point $point, BoxInterface $requestedDimensions)
    {
        throw new \BadMethodCallException(sprintf('Users of this trait need to implement the "createCropManipulation" method but "%s" does not.', get_class($this)), 1501685109418);
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
        $ratioMode = $this->options['ratioMode'] ?: ResizeCropManipulationInterface::RATIOMODE_INSET;

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
