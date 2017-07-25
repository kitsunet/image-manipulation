<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\ResourceManagement\PersistentResource;

/**
 * @Flow\Scope("singleton")
 */
class ImageSizes
{
    /**
     * @var VariableFrontend
     */
    protected $imageSizeCache;

    /**
     * @param PersistentResource $resource
     * @return mixed
     */
    public function getSizeForResource(PersistentResource $resource)
    {
        $cacheIdentifier = $resource->getCacheEntryIdentifier();

        $imageSize = $this->imageSizeCache->get($cacheIdentifier);
        if ($imageSize === false) {
            $imageSize = $this->calculateImageSize($resource->getStream());
            $this->imageSizeCache->set($cacheIdentifier, $imageSize);
        }

        return $imageSize;
    }

    /**
     * @param PersistentResource $resource
     * @param BoxInterface $imageSize
     */
    public function setSizeForResource(PersistentResource $resource, BoxInterface $imageSize)
    {
        $this->imageSizeCache->set($resource->getCacheEntryIdentifier(), [
            'width' => $imageSize->getWidth(),
            'height' => $imageSize->getHeight()
        ]);
    }

    /**
     * @param resource $stream
     * @return array
     * @throws ImageFileException
     */
    protected function calculateImageSize($stream)
    {
        try {
            $imagineImage = $this->imagineService->read($stream);
            $sizeBox = $imagineImage->getSize();
            $imageSize = ['width' => $sizeBox->getWidth(), 'height' => $sizeBox->getHeight()];
        } catch (\Exception $e) {
            throw new ImageFileException(sprintf('The given resource was not an image file your choosen driver can open. The original error was: %s', $e->getMessage()), 1336662898, $e);
        }

        return $imageSize;
    }
}
