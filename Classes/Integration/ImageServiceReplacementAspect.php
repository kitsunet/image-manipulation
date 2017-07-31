<?php
namespace Kitsunet\ImageManipulation\Integration;

use Kitsunet\ImageManipulation\ImageBlob\ImageServiceInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageSizes;
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
     * @var ImageServiceInterface
     */
    protected $imageService;

    /**
     * @Flow\Inject
     * @var ImageSizes
     */
    protected $imageSizes;

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

        return $this->imageService->processResource($arguments['originalResource'], $manipulationsDescriptions);
    }

    /**
     * @param JoinPointInterface $joinPoint
     * @return array
     *
     * @Flow\Around("method(Neos\Media\Domain\Service\ImageService->getImageSize())")
     */
    public function replaceGetImageSize(JoinPointInterface $joinPoint)
    {
        $arguments = $joinPoint->getMethodArguments();
        return $this->imageSizes->getSizeForResource($arguments['resource']);
    }
}
