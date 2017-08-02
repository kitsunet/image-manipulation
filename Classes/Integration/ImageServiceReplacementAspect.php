<?php
namespace Kitsunet\ImageManipulation\Integration;

use Kitsunet\ImageManipulation\ImageBlob\ImageBlobFactoryInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageServiceInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageSizes;
use Kitsunet\ImageManipulation\ImageBlob\ResourceProcessorInterface;
use Kitsunet\ImageManipulation\Media\ManipulationFactory;
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
     * @var ResourceProcessorInterface
     */
    protected $imageService;

    /**
     * @Flow\Inject
     * @var ImageSizes
     */
    protected $imageSizes;

    /**
     * @Flow\Inject
     * @var ManipulationFactory
     */
    protected $manipulationFactory;

    /**
     * @param JoinPointInterface $joinPoint
     * @return array
     *
     * @Flow\Around("method(Neos\Media\Domain\Service\ImageService->processImage())")
     */
    public function replaceProcessImage(JoinPointInterface $joinPoint)
    {
        $arguments = $joinPoint->getMethodArguments();
        $manipulations = $this->manipulationFactory->convertAdjustments($arguments['adjustments']);
        return $this->imageService->processResource($arguments['originalResource'], $manipulations);
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
