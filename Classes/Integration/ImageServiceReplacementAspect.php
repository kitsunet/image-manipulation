<?php
namespace Kitsunet\ImageManipulation\Integration;

use Kitsunet\ImageManipulation\Imagine\ImageService;
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
     * @param JoinPointInterface $joinPoint
     * @return array
     *
     * @Flow\Around("method(Neos\Media\Domain\Service\ImageService->processImage())")
     */
    public function replaceProcessImage(JoinPointInterface $joinPoint)
    {
        $arguments = $joinPoint->getMethodArguments();
        return $this->imageService->processImage($arguments['originalResource'], $arguments['adjustments']);
    }
}
