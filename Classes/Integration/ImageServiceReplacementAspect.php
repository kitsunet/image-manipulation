<?php
namespace Kitsunet\ImageManipulation\Integration;

use Kitsunet\ImageManipulation\Imagine\ImageService;
use Kitsunet\ImageManipulation\Media\ManipulationDescriptionFactory;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Aop\JoinPointInterface;

/**
 * @Flow\Scope("singleton")
 * @Flow\Aspect()
 */
class ImageServiceReplacementAspect
{
    /**
     * @Flow\Inject
     * @var ImageService
     */
    protected $imageService;

    /**
     * @Flow\Inject
     * @var ManipulationDescriptionFactory
     */
    protected $manipulationDescriptionFactory;

    /**
     * @param JoinPointInterface $joinPoint
     * @return array
     *
     * @Flow\Around("method(Neos\Media\Domain\Service\ImageService->processImage())")
     */
    public function replaceProcessImage(JoinPointInterface $joinPoint)
    {
        $arguments = $joinPoint->getMethodArguments();
        $manipulationsDescriptions = $this->manipulationDescriptionFactory->convertAdjustments($arguments['adjustments']);

        return $this->imageService->processImage($arguments['originalResource'], $manipulationsDescriptions);
    }
}
