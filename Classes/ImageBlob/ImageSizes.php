<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

use Kitsunet\ImageManipulation\Blob\BlobMetadata;
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
     * @Flow\Inject
     * @var ImageBlobFactoryInterface
     */
    protected $imageBlobFactory;

    /**
     * @param PersistentResource $resource
     * @return array
     */
    public function getSizeForResource(PersistentResource $resource): array
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
    protected function calculateImageSize($stream): array
    {
        try {
            $blob = $this->imageBlobFactory->create($stream, new BlobMetadata([]));
            $sizeBox = $blob->getSize();
            $imageSize = ['width' => $sizeBox->getWidth(), 'height' => $sizeBox->getHeight()];
        } catch (\Exception $e) {
            throw new ImageFileException(sprintf('The given resource was not an image file your choosen driver can open. The original error was: %s', $e->getMessage()), 1336662898, $e);
        }

        return $imageSize;
    }
}
